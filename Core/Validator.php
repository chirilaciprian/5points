<?php

namespace Core;

use DateTime;

class Validator
{
    public static function string(mixed $value, int $min = 1, int|float $max = INF): bool
    {
        if (!is_string($value)) {
            return false;
        }

        $length = mb_strlen(trim($value));
        return $length >= $min && $length <= $max;
    }

    public static function decimal(mixed $value, float $min = 0, float $max = INF, int $decimals = 2): bool
    {
        if (!is_string($value) || !preg_match('/^(\d+(\.\d{1,' . $decimals . '})?|\.\d{1,' . $decimals . '})$/D', $value)) {
            return false;
        }

        return (float)$value >= $min && (float)$value <= $max;
    }

    public static function date(mixed $value, string $format = 'Y-m-d'): bool
    {
        if (!is_string($value)) {
            return false;
        }

        $date = DateTime::createFromFormat('!' . $format, $value);
        return $date !== false && $date->format($format) === $value;
    }
}
