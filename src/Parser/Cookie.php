<?php

declare(strict_types=1);

namespace PsrJwt\Parser;

use PsrJwt\Parser\ArgumentsInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Find the JSON Web Token in a cookie. This is a good way to pass around JWTs,
 * make sure the cookie is a 'secure' one.
 */
class Cookie implements ArgumentsInterface
{
    /**
     * @var array $arguments
     */
    private $arguments;

    /**
     * @param array $arguments
     */
    public function __construct(array $arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * @param ServerRequestInterface $request
     * @return string
     */
    public function parse(ServerRequestInterface $request): string
    {
        return $request->getCookieParams()[$this->arguments['token_key']] ?? '';
    }
}
