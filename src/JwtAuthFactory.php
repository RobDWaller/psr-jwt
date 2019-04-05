<?php

declare(strict_types=1);

namespace PsrJwt;

use PsrJwt\JwtAuthMiddleware;
use PsrJwt\JwtAuthHandler;
use PsrJwt\JwtAuthInvokable;

class JwtAuthFactory
{
    public static function middleware(): JwtAuthMiddleware
    {
        return new JwtAuthMiddleware();
    }

    public function handler($tokenKey, $secret): JwtAuthHandler
    {
        return new JwtAuthHandler($tokenKey, $secret);
    }

    public function invokable($tokenKey, $secret): JwtAuthInvokable
    {
        $handler = new JwtAuthHandler($tokenKey, $secret);

        return new JwtAuthInvokable($handler);
    }
}
