<?php

namespace Core;

use Exception;

class ValidationException extends Exception
{
    public readonly array $errors;

    /**
     * @param array<string, string> $errors Error message per field name.
     *
     * @throws static
     */
    public static function throw(array $errors): never
    {
        $instance = new static('Please correct the highlighted fields.');

        $instance->errors = $errors;

        throw $instance;
    }
}
