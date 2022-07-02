<?php

declare(strict_types=1);

namespace PsrJwt\Factory;

use PsrJwt\Handler\Html;
use PsrJwt\Handler\Json;
use PsrJwt\Handler\Config;
use PsrJwt\Auth\Authorise;
use PsrJwt\Factory\Retriever;

class Handler
{
    public static function html(string $key, string $secret, string $response): Html
    {
        return new Html(
            new Config($secret, $response),
            Retriever::make($key),
            new Authorise()
        );
    }

    public static function json(string $key, string $secret, array $response): Json
    {
        return new Json(
            new Config($secret, $response),
            Retriever::make($key),
            new Authorise()
        );
    }
}
