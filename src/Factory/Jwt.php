<?php

declare(strict_types=1);

namespace PsrJwt\Factory;

use ReallySimpleJWT\Build;
use ReallySimpleJWT\Validate;
use ReallySimpleJWT\Encode;
use ReallySimpleJWT\Parse;
use ReallySimpleJWT\Jwt as RSJwt;

/**
 * This library wraps around the ReallySimpleJWT library to provide token
 * creation and validation functionality. This factory class provides a builder
 * and parser method to provide quick access to this functionality.
 */
class Jwt
{
    /**
     * ALlow for the generation of JSON Web Tokens
     *
     * @return Build
     */
    public function builder(): Build
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
     * @return RSParse
     */
    public function parser(string $token, string $secret): Parse
    {
        $jwt = new RSJwt($token, $secret);

        return new Parse(
            $jwt,
            new Validate(),
            new Encode()
        );
    }
}
