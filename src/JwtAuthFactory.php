<?php

declare(strict_types=1);

namespace PsrJwt;

use PsrJwt\JwtAuthMiddleware;

class JwtAuthFactory
{
    public static function middleware(): JwtAuthMiddleware
    {
        return new JwtAuthMiddleware();
    }
}
