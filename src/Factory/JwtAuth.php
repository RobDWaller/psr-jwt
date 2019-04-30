<?php

declare(strict_types=1);

namespace PsrJwt\Factory;

use PsrJwt\JwtAuthMiddleware;
use PsrJwt\Auth\Authenticate;
use PsrJwt\JwtAuthInvokable;

class JwtAuth
{
    public static function middleware(string $tokenKey, string $secret): JwtAuthMiddleware
    {
        $auth = new Authenticate($tokenKey, $secret);

        return new JwtAuthMiddleware($auth);
    }
}
