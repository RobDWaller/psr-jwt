<?php

declare(strict_types=1);

namespace PsrJwt\Parser;

use PsrJwt\Parser\ParserInterface;
use Psr\Http\Message\ServerRequestInterface;

class Bearer implements ParserInterface
{
    private $arguments;

    public function __construct(array $arguments)
    {
        $this->arguments = $arguments;
    }

    public function parse(ServerRequestInterface $request): array
    {
        $authorization = $request->getHeader('authorization');

        $bearer = array_filter($authorization, function ($item) {
            return (bool) preg_match('/^bearer\s.+/', $item);
        });

        $token = explode(' ', $bearer[0] ?? '')[1] ?? '';

        return !empty($token) ? [$this->arguments['token_key'] => $token] : [];
    }
}
