<?php

declare(strict_types=1);

namespace PsrJwt\Parser;

use PsrJwt\Parser\ParserInterface;
use Psr\Http\Message\ServerRequestInterface;

class Body implements ParserInterface
{
    private $arguments;

    public function __construct(array $arguments)
    {
        $this->arguments = $arguments;
    }

    public function parse(ServerRequestInterface $request): array
    {
        $body = $request->getParsedBody();

        if (is_array($body) && isset($body[$this->arguments['token_key']])) {
            return $body;
        }

        return $this->parseBodyObject($request);
    }

    private function parseBodyObject(ServerRequestInterface $request): array
    {
        $body = $request->getParsedBody();

        if (is_object($body) && isset($body->{$this->arguments['token_key']})) {
            return [$this->arguments['token_key'] => $body->{$this->arguments['token_key']}];
        }

        return [];
    }
}