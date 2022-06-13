<?php

declare(strict_types=1);

namespace PsrJwt\Parser;

use Psr\Http\Message\ServerRequestInterface;

/**
 * The parse method is used to retrieve the JWT token from the request object.
 */
interface ParserInterface
{
    public function parse(ServerRequestInterface $request): string;
}
