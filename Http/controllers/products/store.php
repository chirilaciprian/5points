<?php

use Core\App;
use Core\Database;
use Core\Response;
use Core\UploadedImage;
use Http\Forms\ProductForm;

$form = ProductForm::validate($_POST, $_FILES);

$image = $form->image()?->store();

try {
    App::resolve(Database::class)->query(
        'INSERT INTO products (name, description, price, image, availability_date, in_stock)
         VALUES (:name, :description, :price, :image, :availability_date, :in_stock)',
        $form->data() + ['image' => $image]
    );
} catch (Throwable $exception) {
    // Do not leave an image behind that no product points to.
    UploadedImage::delete($image);

    throw $exception;
}

json(['message' => 'Product created successfully.'], Response::CREATED);
