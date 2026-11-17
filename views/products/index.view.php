<?php

use Core\UploadedImage;
use Http\Forms\ProductForm;

$title = 'Products';

?>
<?php require base_path('views/partials/head.php') ?>

<header class="page-header">
    <h1>Products</h1>

    <button type="button" class="btn btn--primary" id="add-product">Add product</button>
</header>

<main>
    <form class="search" id="search-form" action="/" method="get" role="search">
        <label for="search" class="sr-only">Search products by name</label>
        <div class="search__field">
            <input type="text" id="search" enterkeyhint="search" name="search" value="<?= e($search) ?>" placeholder="Search by name&hellip;" maxlength="255" autocomplete="off">
            <button type="submit" class="search__button" aria-label="Search">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
                    <circle cx="11" cy="11" r="7"></circle>
                    <line x1="16.5" y1="16.5" x2="21" y2="21"></line>
                </svg>
            </button>
        </div>
    </form>

    <section id="product-list" class="product-list" aria-label="Products">
        <?php require base_path('views/products/partials/list.view.php') ?>
    </section>
</main>

<?php // Create / edit form, opened as a modal over the list. ?>
<dialog class="dialog" id="product-dialog" aria-labelledby="product-dialog-title">
    <form id="product-form" action="/products" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="">

        <header class="dialog__header">
            <h2 id="product-dialog-title">Add product</h2>
            <button type="button" class="dialog__close" data-close aria-label="Close">&times;</button>
        </header>

        <div class="dialog__body">
            <p class="alert alert--error" id="product-form-error" role="alert" hidden></p>

            <div class="field">
                <label for="product-name">Name <span class="field__required" aria-hidden="true">*</span></label>
                <input type="text" id="product-name" name="name" required maxlength="<?= ProductForm::NAME_MAX ?>" aria-describedby="product-name-error">
                <p class="field__error" id="product-name-error" data-error-for="name" hidden></p>
            </div>

            <div class="field">
                <label for="product-description">Description</label>
                <textarea id="product-description" name="description" rows="4" maxlength="<?= ProductForm::DESCRIPTION_MAX ?>" aria-describedby="product-description-error"></textarea>
                <p class="field__error" id="product-description-error" data-error-for="description" hidden></p>
            </div>

            <div class="field-row">
                <div class="field">
                    <label for="product-price">Price <span class="field__required" aria-hidden="true">*</span></label>
                    <input type="number" id="product-price" name="price" required min="0" max="<?= ProductForm::PRICE_MAX ?>" step="0.01" inputmode="decimal" aria-describedby="product-price-error">
                    <p class="field__error" id="product-price-error" data-error-for="price" hidden></p>
                </div>

                <div class="field">
                    <label for="product-availability-date">Availability date</label>
                    <input type="date" id="product-availability-date" name="availability_date" aria-describedby="product-availability-date-error">
                    <p class="field__error" id="product-availability-date-error" data-error-for="availability_date" hidden></p>
                </div>
            </div>

            <div class="field">
                <label for="product-image">Image</label>

                <div class="image-field">
                    <span class="thumbnail thumbnail--large" id="product-image-placeholder" aria-hidden="true">No image</span>
                    <img class="thumbnail thumbnail--large thumbnail--image" id="product-image-preview" alt="Image preview" hidden>

                    <div class="image-field__controls">
                        <input type="file" id="product-image" name="image" accept="<?= e(implode(',', array_keys(UploadedImage::TYPES))) ?>" data-max-size="<?= UploadedImage::maxSize() ?>" aria-describedby="product-image-hint product-image-error">
                        <p class="field__hint" id="product-image-hint">
                            <?= e(UploadedImage::formats()) ?>, up to <?= e(round(UploadedImage::maxSize() / 1024 / 1024, 1)) ?> MB.
                        </p>

                        <div class="field--checkbox" id="product-remove-image-field" hidden>
                            <input type="checkbox" id="product-remove-image" name="remove_image" value="1">
                            <label for="product-remove-image">Remove current image</label>
                        </div>
                    </div>
                </div>

                <p class="field__error" id="product-image-error" data-error-for="image" hidden></p>
            </div>

            <div class="field field--checkbox">
                <input type="checkbox" id="product-in-stock" name="in_stock" value="1">
                <label for="product-in-stock">In stock</label>
            </div>
        </div>

        <footer class="dialog__footer">
            <button type="button" class="btn" data-close>Cancel</button>
            <button type="submit" class="btn btn--primary" id="product-form-submit">Save product</button>
        </footer>
    </form>
</dialog>

<?php // Confirmation shown before a product is permanently deleted. ?>
<dialog class="dialog dialog--small" id="delete-dialog" aria-labelledby="delete-dialog-title">
    <header class="dialog__header">
        <h2 id="delete-dialog-title">Delete product</h2>
        <button type="button" class="dialog__close" data-close aria-label="Close">&times;</button>
    </header>

    <div class="dialog__body">
        <p>
            Are you sure you want to permanently delete <strong id="delete-dialog-name"></strong>?
            This action cannot be undone.
        </p>
    </div>

    <footer class="dialog__footer">
        <button type="button" class="btn" data-close>Cancel</button>
        <button type="button" class="btn btn--danger" id="delete-dialog-confirm">Delete</button>
    </footer>
</dialog>

<?php // Success / error messages for every operation. ?>
<div class="toasts" id="toasts" role="status" aria-live="polite"></div>

<script type="importmap"><?= import_map('js') ?></script>
<script type="module" src="<?= e(asset('js/app.js')) ?>"></script>

<?php require base_path('views/partials/footer.php') ?>
