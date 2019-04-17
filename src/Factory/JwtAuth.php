<?php

declare(strict_types=1);

namespace PsrJwt\Factory;

use PsrJwt\JwtAuthMiddleware;
use PsrJwt\JwtAuthHandler;
use PsrJwt\JwtAuthInvokable;

class JwtAuth
{
    public static function middleware(): JwtAuthMiddleware
    {
        return new JwtAuthMiddleware();
    }

    public static function handler($tokenKey, $secret): JwtAuthHandler
    {
        return new JwtAuthHandler($tokenKey, $secret);
    }

    public static function invokable($tokenKey, $secret): JwtAuthInvokable
    {
        $handler = new JwtAuthHandler($tokenKey, $secret);

        return new JwtAuthInvokable($handler);
    }
}
