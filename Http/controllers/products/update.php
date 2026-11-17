<?php

use Core\App;
use Core\Database;
use Core\UploadedImage;
use Http\Forms\ProductForm;

$db = App::resolve(Database::class);

// find the corresponding product
$product = $db->query('SELECT id, image FROM products WHERE id = :id', [
    'id' => id_or_abort($_POST['id'] ?? null),
])->findOrFail();

// validate the form
$form = ProductForm::validate($_POST, $_FILES);

// a new upload replaces the current image, "remove image" clears it and
// otherwise the current image is kept
$image = match (true) {
    $form->image() !== null => $form->image()->store(),
    $form->removesImage() => null,
    default => $product['image'],
};

// if there are no validation errors, update the record
try {
    $db->query(
        'UPDATE products
         SET name = :name,
             description = :description,
             price = :price,
             image = :image,
             availability_date = :availability_date,
             in_stock = :in_stock
         WHERE id = :id',
        $form->data() + ['image' => $image, 'id' => (int) $product['id']]
    );
} catch (Throwable $exception) {
    if ($image !== $product['image']) {
        UploadedImage::delete($image);
    }

    throw $exception;
}

// the previous file is only removed once the record no longer points to it
if ($image !== $product['image']) {
    UploadedImage::delete($product['image']);
}

json(['message' => 'Product updated successfully.']);
