<?php

declare(strict_types=1);

namespace PsrJwt\Parser;

use PsrJwt\Parser\ParserInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Find the JSON Web Token in the authorization header as a bearer token. This
 * is the ideal means for passing around JWTs.
 */
class Bearer implements ParserInterface
{
    /**
     * @param ServerRequestInterface $request
     * @return string
     */
    public function parse(ServerRequestInterface $request): string
    {
        $authorization = $request->getHeader('authorization');

        $bearer = array_filter($authorization, function ($item) {
            return (bool) preg_match('/^Bearer\s.+/', $item);
        });

        $token = explode(' ', $bearer[0] ?? '')[1] ?? '';

        return !empty($token) ? $token : '';
    }
}
