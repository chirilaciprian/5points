<?php

// Database settings can be overridden with the DB_* environment variables.
$env = fn (string $key, string $default) => getenv($key) !== false ? getenv($key) : $default;

return [
    'database' => [
        'connection' => [
            'host' => $env('DB_HOST', '127.0.0.1'),
            'port' => (int) $env('DB_PORT', '3306'),
            'dbname' => $env('DB_DATABASE', '5points'),
            'charset' => 'utf8mb4',
        ],
        'username' => $env('DB_USERNAME', 'dev'),
        'password' => $env('DB_PASSWORD', 'dev'),
    ],
    'uploads' => [
        'directory' => 'public/uploads/products',
        'url' => '/uploads/products',
        'max_size' => 2 * 1024 * 1024,
    ],
    'pagination' => [
        'per_page' => 10,
    ],
];
