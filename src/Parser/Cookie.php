<?php

declare(strict_types=1);

namespace PsrJwt\Parser;

use PsrJwt\Parser\ParserInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Find the JSON Web Token in a cookie. This is a good way to pass around JWTs,
 * make sure the cookie is a 'secure' one.
 */
class Cookie implements ParserInterface
{
    /**
     * @var string $tokenKey
     */
    private $tokenKey;

    /**
     * @param string $tokenKey
     */
    public function __construct(string $tokenKey)
    {
        $this->tokenKey = $tokenKey;
    }

    /**
     * @param ServerRequestInterface $request
     * @return string
     */
    public function parse(ServerRequestInterface $request): string
    {
        return $request->getCookieParams()[$this->tokenKey] ?? '';
    }
}
