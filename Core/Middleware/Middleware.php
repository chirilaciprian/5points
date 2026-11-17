<?php

namespace Core\Middleware;

use Exception;

class Middleware
{
    public const MAP = [
        'csrf' => VerifyCsrfToken::class,
    ];

    /**
     * @throws Exception
     */
    public static function resolve(?string $key): void
    {
        if (!$key) {
            return;
        }

        $middleware = static::MAP[$key] ?? false;

        if (!$middleware) {
            throw new Exception("No matching middleware found for key '{$key}'.");
        }

        (new $middleware)->handle();
    }
}
