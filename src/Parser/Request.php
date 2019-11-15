<?php

declare(strict_types=1);

namespace PsrJwt\Parser;

use PsrJwt\Parser\Parse;
use Psr\Http\Message\ServerRequestInterface;

class Request
{
    private $parse;

    public function __construct(Parse $parse)
    {
        $this->parse = $parse;

        $this->parse->addParser(\PsrJwt\Parser\Bearer::class);
        $this->parse->addParser(\PsrJwt\Parser\Cookie::class);
        $this->parse->addParser(\PsrJwt\Parser\Body::class);
        $this->parse->addParser(\PsrJwt\Parser\Query::class);
    }

    public function hasToken(ServerRequestInterface $request): bool
    {
        return !empty($this->parse->findToken($request));
    }

    public function findToken(ServerRequestInterface $request): string
    {
        return $this->parse->findToken($request);
    }
}