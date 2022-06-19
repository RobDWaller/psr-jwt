<?php

declare(strict_types=1);

namespace PsrJwt\Factory;

use PsrJwt\Retrieve;
use PsrJwt\Location\Bearer;
use PsrJwt\Location\Body;
use PsrJwt\Location\Cookie;
use PsrJwt\Location\Query;

class Retriever
{
    public static function make(string $key): Retrieve
    {
        return new Retrieve([
            new Bearer(),
            new Body($key),
            new Cookie($key),
            new Query($key)
        ]);
    }
}