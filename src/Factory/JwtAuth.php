<?php

declare(strict_types=1);

namespace PsrJwt\Factory;

use PsrJwt\JwtAuthMiddleware;
use PsrJwt\Handler\Auth;
use PsrJwt\Handler\Json;
use PsrJwt\JwtAuthInvokable;

/**
 * Easily add the PSR-JWT middleware to PSR compliant frameworks such as
 * Zend Expressive and Slim PHP.
 */
class JwtAuth
{
    /**
     * Add the middleware to the relevant framework and return a text / html
     * response on validation failure.
     *
     * @param string $tokenKey
     * @param string $secret
     * @param string $body
     * @return JwtAuthMiddleware
     */
    public static function middleware(string $secret, string $tokenKey = '', string $body = ''): JwtAuthMiddleware
    {
        $auth = new Auth($secret, $tokenKey, $body);

        return new JwtAuthMiddleware($auth);
    }

    /**
     * Add the middleware to the relevant framework and return a JSON response
     * on validation failure.
     *
     * @param string $tokenKey
     * @param string $secret
     * @param array $body
     * @return JwtAuthMiddleware
     */
    public static function json(string $secret, string $tokenKey = '', array $body = []): JwtAuthMiddleware
    {
        $auth = new Json($secret, $tokenKey, $body);

        return new JwtAuthMiddleware($auth);
    }
}
