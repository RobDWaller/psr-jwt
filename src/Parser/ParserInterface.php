<?php

namespace PsrJwt\Parser;

use Psr\Http\Message\ServerRequestInterface;

interface ParserInterface
{
    public function parse(ServerRequestInterface $request): array;
}
