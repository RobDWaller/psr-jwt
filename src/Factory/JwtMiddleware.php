<?php

declare(strict_types=1);

namespace PsrJwt\Factory;

use PsrJwt\JwtAuthMiddleware;
use PsrJwt\Handler\Html;
use PsrJwt\Handler\Json;

/**
 * Factory to easily add the PSR-JWT middleware to PSR compliant frameworks such
 * as Slim PHP.
 */
class JwtMiddleware
{
    /**
     * Add the middleware to the relevant framework and return a text / html
     * response on authorisation failure.
     */
    public static function html(string $secret, string $tokenKey = '', string $body = ''): JwtAuthMiddleware
    {
        $auth = new Html($secret, $tokenKey, $body);

        return new JwtAuthMiddleware($auth);
    }

    /**
     * Add the middleware to the relevant framework and return a JSON response
     * on authorisation failure.
     *
     * @param mixed[] $body
     */
    public static function json(string $secret, string $tokenKey = '', array $body = []): JwtAuthMiddleware
    {
        $auth = new Json($secret, $tokenKey, $body);

        return new JwtAuthMiddleware($auth);
    }
}
