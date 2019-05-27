<?php

declare(strict_types=1);

namespace PsrJwt\Factory;

use PsrJwt\JwtAuthMiddleware;
use PsrJwt\Handler\Auth;
use PsrJwt\JwtAuthInvokable;

/**
 * Easily add the PSR-JWT middleware to PSR compliant frameworks such as
 * Zend Expressive and Slim PHP.
 */
class JwtAuth
{
    /**
     * Add the middleware to the relevant framework.
     *
     * @param string $tokenKey
     * @param string $secret
     * @return JwtAuthMiddleware
     * @todo TokenKey and Secret are the wrong way around.
     */
    public static function middleware(string $secret, string $tokenKey = '', string $body = ''): JwtAuthMiddleware
    {
        $auth = new Auth($secret, $tokenKey, $body);

        return new JwtAuthMiddleware($auth);
    }
}
