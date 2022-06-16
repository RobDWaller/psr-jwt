<?php

declare(strict_types=1);

namespace PsrJwt\Auth;

use Psr\Http\Message\ServerRequestInterface;
use PsrJwt\Factory\Jwt;
use PsrJwt\Parser\Parse;
use PsrJwt\Parser\Request;
use PsrJwt\Parser\ParseException;
use PsrJwt\Validation\Validate;
use PsrJwt\Status\Status;
use ReallySimpleJWT\Exception\ValidateException;
use ReallySimpleJWT\Exception\ParsedException;

/**
 * Retrieve the JSON Web Token from the request and attempt to parse and
 * validate it.
 */
class Authorise implements AuthoriseInterface
{
    /**
     * Find, parse and validate the JSON Web Token.
     */
    public function authorise(string $token, string $secret): Status
    {
        try {
            $jwt = new Jwt();
            $validator = $jwt->validator($token, $secret);
            $validator->signature()->expiration()->notBefore();
        } catch (ValidateException | ParsedException $e) {
            if (in_array($e->getCode(), [3, 4, 5], true)) {
                return new Status(400, 'Bad Request: ' . $e->getMessage());
            }
        }

        return new Status(200, 'Ok');
    }
}
