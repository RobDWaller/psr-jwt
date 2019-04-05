<?php

declare(strict_types=1);

namespace PsrJwt;

use ReallySimpleJWT\Build;
use ReallySimpleJWT\Validate;
use ReallySimpleJWT\Encode;
use ReallySimpleJWT\Parse;
use ReallySimpleJWT\Jwt;

class JwtFactory
{
    public static function builder(): Build
    {
        return new Build(
            'JWT',
            new Validate(),
            new Encode()
        );
    }

    public static function parser(string $token, string $secret): Parse
    {
        $jwt = new Jwt($token, $secret);

        return new Parse(
            $jwt,
            new Validate(),
            new Encode()
        );
    }
}
