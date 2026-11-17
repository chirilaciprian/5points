<?php

use Core\App;
use Core\Container;
use Core\Database;

$container = new Container();

$container->bind('config', function () {
    static $config;

    return $config ??= require base_path('config.php');
});

$container->bind(Database::class, function () use ($container) {
    $config = $container->resolve('config')['database'];

    return new Database($config['connection'], $config['username'], $config['password']);
});

App::setContainer($container);
