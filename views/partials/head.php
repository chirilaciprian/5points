<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title><?= e($title ?? 'Product Management') ?></title>
    <link rel="icon" href="data:,">
    <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
</head>

<body>
    <div class="container">
