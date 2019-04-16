<?php

declare(strict_types=1);

namespace PsrJwt\Parser;

use PsrJwt\Parser\ParserInterface;
use Psr\Http\Message\ServerRequestInterface;

class Cookie implements ParserInterface
{
    private $arguments;

    public function __construct(array $arguments)
    {
        $this->arguments = $arguments;
    }

    public function parse(ServerRequestInterface $request): string
    {
        return $request->getCookieParams()[$this->arguments['token_key']] ?? '';
    }
}
