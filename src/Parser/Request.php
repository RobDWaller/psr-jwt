<?php

declare(strict_types=1);

namespace PsrJwt\Parser;

use PsrJwt\Parser\Parse;
use Psr\Http\Message\ServerRequestInterface;
use PsrJwt\Parser\Bearer;
use PsrJwt\Parser\Cookie;
use PsrJwt\Parser\Body;
use PsrJwt\Parser\Query;
use PsrJwt\Parser\ParseException;

class Request
{
    private $parse;

    public function __construct(Parse $parse)
    {
        $this->parse = $parse;
    }

    public function parse(ServerRequestInterface $request, string $tokenKey): string
    {
        $this->parse->addParser(new Bearer());
        $this->parse->addParser(new Cookie($tokenKey));
        $this->parse->addParser(new Body($tokenKey));
        $this->parse->addParser(new Query($tokenKey));

        $token = $this->parse->findToken($request);

        if (!empty($token)) {
            return $token;
        }

        throw new ParseException('JSON Web Token not set in request.');
    }
}