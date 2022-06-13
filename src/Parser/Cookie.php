<?php

declare(strict_types=1);

namespace PsrJwt\Parser;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Find the JSON Web Token in a cookie. This is a good way to pass around JWTs,
 * make sure the cookie is a 'secure' one.
 */
class Cookie implements ParserInterface
{
    private string $tokenKey;

    public function __construct(string $tokenKey)
    {
        $this->tokenKey = $tokenKey;
    }

    public function parse(ServerRequestInterface $request): string
    {
        return $request->getCookieParams()[$this->tokenKey] ?? '';
    }
}
