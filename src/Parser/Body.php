<?php

declare(strict_types=1);

namespace PsrJwt\Parser;

use PsrJwt\Parser\ArgumentsInterface;
use Psr\Http\Message\ServerRequestInterface;

class Body implements ArgumentsInterface
{
    private $arguments;

    public function __construct(array $arguments)
    {
        $this->arguments = $arguments;
    }

    public function parse(ServerRequestInterface $request): string
    {
        $body = $request->getParsedBody();

        if (is_array($body) && isset($body[$this->arguments['token_key']])) {
            return $body[$this->arguments['token_key']];
        }

        return $this->parseBodyObject($request);
    }

    private function parseBodyObject(ServerRequestInterface $request): string
    {
        $body = $request->getParsedBody();

        if (is_object($body) && isset($body->{$this->arguments['token_key']})) {
            return $body->{$this->arguments['token_key']};
        }

        return '';
    }
}
