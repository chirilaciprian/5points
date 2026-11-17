<?php $title = "Error {$code}" ?>
<?php require base_path('views/partials/head.php') ?>

<main class="error-page">
    <p class="error-page__code"><?= e($code) ?></p>
    <h1><?= e($message) ?></h1>

    <p>
        <a href="/" class="btn btn--primary">Back to the products</a>
    </p>
</main>

<?php require base_path('views/partials/footer.php') ?>
