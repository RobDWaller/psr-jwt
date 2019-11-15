<?php

declare(strict_types=1);

namespace PsrJwt\Parser;

use PsrJwt\Parser\Parse;

class Request
{
    private $parse;

    public function __construct(Parse $parse)
    {
        $this->parse = $parse;
    }
}