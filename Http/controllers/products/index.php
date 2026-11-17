<?php

use Core\App;
use Core\Database;
use Core\Paginator;

$db = App::resolve(Database::class);
$perPage = App::resolve('config')['pagination']['per_page'];

$search = is_string($_GET['search'] ?? null) ? mb_substr(trim($_GET['search']), 0, 255) : '';
$page = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT) ?: 1;

$like = '%' . str_replace(['|', '%', '_'], ['||', '|%', '|_'], $search) . '%';

$total = (int) $db->query(
    "SELECT COUNT(*) FROM products WHERE name LIKE :search ESCAPE '|'",
    ['search' => $like]
)->value();

$paginator = new Paginator($total, $perPage, $page);

$products = $db->query(
    "SELECT id, name, description, price, image, availability_date, in_stock
     FROM products
     WHERE name LIKE :search ESCAPE '|'
     ORDER BY id DESC
     LIMIT :limit OFFSET :offset",
    [
        'search' => $like,
        'limit' => $paginator->perPage,
        'offset' => $paginator->offset(),
    ]
)->get();

$data = [
    'products' => $products,
    'paginator' => $paginator,
    'search' => $search,
];

header('Vary: Accept');

if (wantsJson()) {
    json([
        'html' => render('products/partials/list.view.php', $data),
        'page' => $paginator->page,
        'total' => $paginator->total,
    ]);
}

view('products/index.view.php', $data);
