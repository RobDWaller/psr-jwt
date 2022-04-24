<?php

declare(strict_types=1);

namespace PsrJwt\Factory;

use ReallySimpleJWT\Tokens;
use ReallySimpleJWT\Build;
use ReallySimpleJWT\Parse;
use ReallySimpleJWT\Validate;

/**
 * PSR-JWT wraps around the ReallySimpleJWT library to provide token
 * creation and validation functionality. This factory class provides a builder
 * and parser method so you can create JSON Web Tokens, and Parse and
 * validate them.
 */
class Jwt
{
    /**
     * ALlow for the generation of JSON Web Tokens.
     *
     * @return Build
     */
    public function builder(): Build
    {
        $tokens = new Tokens();

        return $tokens->builder();
    }

    /**
     * Allow for the parsing of JSON Web Tokens.
     *
     * @return Parse
     */
    public function parser(string $token, string $secret): Parse
    {
        $tokens = new Tokens();

        return $tokens->parser($token, $secret);
    }

    /**
     * Allow for the validation JSON Web Tokens.
     *
     * @return Validate
     */
    public function validator(string $token, string $secret): Validate
    {
        $tokens = new Tokens();

        return $tokens->validator($token, $secret);
    }
}
