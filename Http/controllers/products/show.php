<?php

use Core\App;
use Core\Database;
use Core\UploadedImage;

$db = App::resolve(Database::class);

$product = $db->query(
    'SELECT id, name, description, price, image, availability_date, in_stock FROM products WHERE id = :id',
    ['id' => id_or_abort($_GET['id'] ?? null)]
)->findOrFail();

json([
    'product' => [
        'id' => (int) $product['id'],
        'name' => $product['name'],
        'description' => $product['description'] ?? '',
        'price' => $product['price'],
        'image_url' => UploadedImage::url($product['image']),
        'availability_date' => $product['availability_date'] ?? '',
        'in_stock' => (bool) $product['in_stock'],
    ],
]);
