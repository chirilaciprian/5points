<?php

namespace Core\Middleware;

use Core\Response;

class VerifyCsrfToken
{
    public function handle(): void
    {
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $_POST['_token'] ?? '';

        if (!is_string($token) || !hash_equals(csrf_token(), $token)) {
            abort(Response::FORBIDDEN);
        }
    }
}
