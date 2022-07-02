<?php

declare(strict_types=1);

namespace PsrJwt\Factory;

use PsrJwt\JwtAuthMiddleware;
use PsrJwt\Factory\Handler;

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
    public static function html(string $secret, string $key = '', string $response = ''): JwtAuthMiddleware
    {
        $auth = Handler::html($key, $secret, $response);

        return new JwtAuthMiddleware($auth);
    }

    /**
     * Add the middleware to the relevant framework and return a JSON response
     * on authorisation failure.
     *
     * @param mixed[] $response
     */
    public static function json(string $secret, string $key = '', array $response = []): JwtAuthMiddleware
    {
        $auth = Handler::json($key, $secret, $response);

        return new JwtAuthMiddleware($auth);
    }
}
