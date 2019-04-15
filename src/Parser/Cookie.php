<?php

declare(strict_types=1);

namespace PsrJwt\Parser;

use PsrJwt\Parser\ParserInterface;
use Psr\Http\Message\ServerRequestInterface;

class Cookie implements ParserInterface
{
    public function parse(ServerRequestInterface $request): array
    {
        return [];
    }
}
