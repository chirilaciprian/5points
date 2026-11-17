<?php

use Core\Response;
use Core\Router;
use Core\ValidationException;

const BASE_PATH = __DIR__ . '/../';

require BASE_PATH . 'vendor/autoload.php';
require BASE_PATH . 'Core/functions.php';

session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
]);

require BASE_PATH . 'bootstrap.php';

$router = new Router();
require BASE_PATH . 'routes.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';

// HTML forms and multipart requests can only be sent as POST, so PATCH and
// DELETE requests are sent as POST with a "_method" field.
$method = is_string($_POST['_method'] ?? null) ? $_POST['_method'] : $_SERVER['REQUEST_METHOD'];

try {
    // PHP silently drops the whole body ($_POST and $_FILES) when it exceeds
    // post_max_size, which would otherwise look like an empty form.
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$_POST && !$_FILES && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
        ValidationException::throw(['image' => 'The upload is too large. Please choose a smaller image.']);
    }

    $router->route($uri, $method);
} catch (ValidationException $exception) {
    json([
        'message' => $exception->getMessage(),
        'errors' => $exception->errors,
    ], Response::UNPROCESSABLE);
} catch (Throwable $exception) {
    // Log the details for the developer, show a generic message to the user.
    error_log((string) $exception);

    abort(Response::SERVER_ERROR);
}
