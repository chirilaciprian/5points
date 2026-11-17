<?php

namespace Core;

class Response
{
    const OK = 200;
    const CREATED = 201;
    const FORBIDDEN = 403;
    const NOT_FOUND = 404;
    const UNPROCESSABLE = 422;
    const SERVER_ERROR = 500;

    const MESSAGES = [
        self::FORBIDDEN => 'Your session has expired. Please refresh the page and try again.',
        self::NOT_FOUND => 'Sorry, we could not find what you were looking for.',
        self::SERVER_ERROR => 'Something went wrong on our side. Please try again later.',
    ];
}
