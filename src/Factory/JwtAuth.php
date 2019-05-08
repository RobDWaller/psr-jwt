<?php

declare(strict_types=1);

namespace PsrJwt\Factory;

use PsrJwt\JwtAuthMiddleware;
use PsrJwt\Auth\Authenticate;
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
    public static function middleware(string $tokenKey, string $secret): JwtAuthMiddleware
    {
        $auth = new Authenticate($tokenKey, $secret);

        return new JwtAuthMiddleware($auth);
    }
}
