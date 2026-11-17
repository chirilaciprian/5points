<?php

use Core\App;
use Core\Database;
use Core\UploadedImage;

$db = App::resolve(Database::class);

$product = $db->query('SELECT id, image FROM products WHERE id = :id', [
    'id' => id_or_abort($_POST['id'] ?? null),
])->findOrFail();

$db->query('DELETE FROM products WHERE id = :id', [
    'id' => (int) $product['id'],
]);

UploadedImage::delete($product['image']);

json(['message' => 'Product deleted successfully.']);
