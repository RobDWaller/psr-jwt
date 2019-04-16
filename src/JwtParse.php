<?php

declare(strict_types = 1);

namespace PsrJwt;

use Psr\Http\Message\ServerRequestInterface;

class JwtParse
{
    private $parsers = [];

    private $arguments;

    public function __construct(array $arguments)
    {
        $this->arguments = $arguments;
    }

    public function addParser(string $parser): void
    {
        $this->parsers[] = $parser;
    }

    public function findToken(ServerRequestInterface $request): string
    {
        foreach ($this->parsers as $parser) {
            $object = new $parser($this->arguments);
            $token = $object->parse($request);
            if (!empty($token)) {
                return $token;
            }
        }

        return '';
    }
}
