<?php

declare(strict_types=1);

namespace PsrJwt\Parser;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Parse the request and find the JSON Web Token.
 */
class Request
{
    private Parse $parse;

    public function __construct(Parse $parse)
    {
        $this->parse = $parse;
    }

    /**
     * To find the JWT token in the request a number of parsers are run against
     * it. The default check is against the authorisation bearer token which is
     * the safest place to put the token.
     */
    public function parse(ServerRequestInterface $request, string $tokenKey): string
    {
        $this->parse->addParser(new Bearer());
        $this->parse->addParser(new Cookie($tokenKey));
        $this->parse->addParser(new Body($tokenKey));
        $this->parse->addParser(new Query($tokenKey));

        return $this->parse->findToken($request);
    }
}
