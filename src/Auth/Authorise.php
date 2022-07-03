<?php

declare(strict_types=1);

namespace PsrJwt\Auth;

use PsrJwt\Factory\Jwt;
use PsrJwt\Parser\Parse;
use PsrJwt\Parser\Request;
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
                $response = $e->getCode() === 3 ? [401, 'Unauthorized'] : [403, 'Forbidden'];
                return new Status($response[0], $response[1] . ': ' . $e->getMessage());
            }
        }

        return new Status(200, 'Ok');
    }
}
