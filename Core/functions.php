<?php

use Core\Response;

function base_path(string $path): string
{
    return BASE_PATH . $path;
}

function e(mixed $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function view(string $path, array $attributes = []): void
{
    extract($attributes);
    require base_path('views/' . $path);
}

function render(string $path, array $attributes = []): string
{
    ob_start();

    try {
        view($path, $attributes);
        return ob_get_contents();
    } finally {
        ob_end_clean();
    }
}

/**
 * URL of a file in public/, versioned with its modification time so browsers
 * fetch it again after it changes.
 */
function asset(string $path): string
{
    $path = ltrim($path, '/');
    $file = base_path('public/' . $path);

    return '/' . $path . (is_file($file) ? '?v=' . filemtime($file) : '');
}

/**
 * Import map (JSON) giving every JavaScript module under public/$directory
 * its versioned URL, so modules loaded through `import` are refreshed after a
 * change just like the entry script.
 */
function import_map(string $directory): string
{
    $public = base_path('public/');
    $imports = [];

    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($public . trim($directory, '/'), FilesystemIterator::SKIP_DOTS)
    );

    foreach ($files as $file) {
        if ($file->getExtension() === 'js') {
            $path = str_replace('\\', '/', substr($file->getPathname(), strlen($public)));
            $imports['/' . $path] = asset($path);
        }
    }

    ksort($imports);

    return json_encode(['imports' => $imports], JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_THROW_ON_ERROR);
}

function wantsJson(): bool
{
    return str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');
}

function json(array $data, int $status = Response::OK): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');

    echo json_encode($data, JSON_THROW_ON_ERROR);
    exit();
}

function abort(int $code = Response::NOT_FOUND, ?string $message = null): never
{
    $message ??= Response::MESSAGES[$code] ?? 'Something went wrong.';

    if (wantsJson()) {
        json(['message' => $message], $code);
    }

    http_response_code($code);
    view('error.view.php', [
        'code' => $code,
        'message' => $message,
    ]);

    exit();
}

function id_or_abort(mixed $value): int
{
    $id = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

    if ($id === false) {
        abort();
    }

    return $id;
}

function csrf_token(): string
{
    return $_SESSION['_token'] ??= bin2hex(random_bytes(32));
}
