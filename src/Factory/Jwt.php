<?php

declare(strict_types=1);

namespace PsrJwt\Factory;

use ReallySimpleJWT\Build;
use ReallySimpleJWT\Validate;
use ReallySimpleJWT\Encode;
use ReallySimpleJWT\Parse;
use ReallySimpleJWT\Jwt as RSJwt;

/**
 * This middleware wraps around the ReallySimpleJWT library. Easy access to the
 * token builder and parser are required.
 */
class Jwt
{
    /**
     * ALlow for the generation of JSON Web Tokens
     *
     * @return Build
     */
    public static function builder(): Build
    {
        return new Build(
            'JWT',
            new Validate(),
            new Encode()
        );
    }

    /**
     * Allow for the parsing and validation of JSON Web Tokens
     *
     * @return Parse
     */
    public static function parser(string $token, string $secret): Parse
    {
        $jwt = new RSJwt($token, $secret);

        return new Parse(
            $jwt,
            new Validate(),
            new Encode()
        );
    }
}
