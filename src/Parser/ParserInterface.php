<?php

namespace PsrJwt\Parser;

use Psr\Http\Message\ServerRequestInterface;

interface ParserInterface
{
    public function __construct(array $arguments);

    public function parse(ServerRequestInterface $request): string;
}
