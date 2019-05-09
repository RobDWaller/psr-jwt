<?php

namespace PsrJwt\Parser;

use Psr\Http\Message\ServerRequestInterface;

/**
 * All parsers require the parse method
 */
interface ParserInterface
{
    /**
     * @param ServerRequestInterface
     * @return string
     */
    public function parse(ServerRequestInterface $request): string;
}
