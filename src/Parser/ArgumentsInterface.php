<?php

namespace PsrJwt\Parser;

use PsrJwt\Parser\ParserInterface;

interface ArgumentsInterface extends ParserInterface
{
    public function __construct(array $arguments);
}
