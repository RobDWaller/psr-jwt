<?php

declare(strict_types=1);

namespace PsrJwt\Helper;

use PsrJwt\Parser\Parse;
use PsrJwt\Parser\Request as RequestParser;
use PsrJwt\Factory\Jwt;
use ReallySimpleJWT\Parsed;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Interact with the request and the JSON web token independent of the
 * middleware and token authorisation process.
 */
class Request
{
    /**
     * Retrieve a JSON Web Token from a request. Returned as a ReallySimpleJWT
     * Parsed object.
     */
    public function getParsedToken(ServerRequestInterface $request, string $tokenKey): Parsed
    {
        $parseRequest = new RequestParser(new Parse());

        $token = $parseRequest->parse($request, $tokenKey);

        $jwt = new Jwt();

        return $jwt->parser($token, '')->parse();
    }

    /**
     * Retrieve the JWT header information from a request.
     */
    public function getTokenHeader(ServerRequestInterface $request, string $tokenKey): array
    {
        return $this->getParsedToken($request, $tokenKey)->getHeader();
    }

    /**
     * Retrieve the JWT payload information from a request.
     */
    public function getTokenPayload(ServerRequestInterface $request, string $tokenKey): array
    {
        return $this->getParsedToken($request, $tokenKey)->getPayload();
    }
}
