<?php

declare(strict_types=1);

namespace PsrJwt\Parser;

use PsrJwt\Parser\ArgumentsInterface;
use Psr\Http\Message\ServerRequestInterface;

class Server implements ArgumentsInterface
{
    private $arguments;

    public function __construct(array $arguments)
    {
        $this->arguments = $arguments;
    }

    public function parse(ServerRequestInterface $request): string
    {
        return $request->getServerParams()[$this->arguments['token_key']] ?? '';
    }
}
