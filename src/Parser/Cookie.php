<?php

declare(strict_types=1);

namespace PsrJwt\Parser;

use PsrJwt\Parser\ArgumentsInterface;
use Psr\Http\Message\ServerRequestInterface;

class Cookie implements ArgumentsInterface
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
