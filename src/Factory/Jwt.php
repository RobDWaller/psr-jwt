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
     */
    public function builder(string $secret): Build
    {
        $tokens = new Tokens();

        return $tokens->builder($secret);
    }

    /**
     * Allow for the parsing of JSON Web Tokens.
     */
    public function parser(string $token): Parse
    {
        $tokens = new Tokens();

        return $tokens->parser($token);
    }

    /**
     * Allow for the validation JSON Web Tokens.
     */
    public function validator(string $token, string $secret): Validate
    {
        $tokens = new Tokens();

        return $tokens->validator($token, $secret);
    }
}
