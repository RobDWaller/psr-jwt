<?php 

declare(strict_types=1);

namespace PsrJwt\Helper;

use PsrJwt\Parser\Parse;
use PsrJwt\Parser\Request as RequestParser;
use PsrJwt\Factory\Jwt;
use ReallySimpleJWT\Parsed;
use Psr\Http\Message\ServerRequestInterface;

class Request
{
    public function getParsedToken(ServerRequestInterface $request, string $tokenKey): Parsed
    {
        $parseRequest = new RequestParser(new Parse());

        $token = $parseRequest->parse($request, $tokenKey);

        $jwt = new Jwt();

        return $jwt->parser($token, '')->parse();
    }
}