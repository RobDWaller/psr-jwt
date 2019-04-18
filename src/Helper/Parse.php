<?php

declare(strict_types = 1);

namespace PsrJwt\Helper;

use Psr\Http\Message\ServerRequestInterface;

class Parse
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

    public function getParsers(): array
    {
        return $this->parsers;
    }

    public function findToken(ServerRequestInterface $request): string
    {
        foreach ($this->getParsers() as $parser) {
            $object = new $parser($this->arguments);
            $token = $object->parse($request);
            if (!empty($token)) {
                return $token;
            }
        }

        return '';
    }
}
