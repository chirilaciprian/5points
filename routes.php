<?php

// The product list: a full page for browsers, a JSON payload for AJAX calls.
$router->get('/', 'products/index.php');
$router->get('/products', 'products/index.php');

// JSON endpoints used by the front end.
$router->get('/product', 'products/show.php');
$router->post('/products', 'products/store.php')->only('csrf');
$router->patch('/product', 'products/update.php')->only('csrf');
$router->delete('/product', 'products/destroy.php')->only('csrf');
