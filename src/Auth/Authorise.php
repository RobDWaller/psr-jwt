<?php

declare(strict_types=1);

namespace PsrJwt\Auth;

use Psr\Http\Message\ServerRequestInterface;
use PsrJwt\Factory\Jwt;
use PsrJwt\Auth\Auth;
use PsrJwt\Parser\Parse;
use PsrJwt\Parser\Request;
use PsrJwt\Parser\ParseException;
use PsrJwt\Validation\Validate;

/**
 * Retrieve the JSON Web Token from the request and attempt to parse and
 * validate it.
 */
class Authorise
{
    /**
     * The secret required to parse and validate the JWT.
     *
     * @var string $secret
     */
    private $secret;

    /**
     * Define which key the JWT can be found under in the request.
     *
     * @var string $tokenKey
     */
    private $tokenKey;

    /**
     * @param string $secret
     * @param string $tokenKey
     */
    public function __construct(string $secret, string $tokenKey)
    {
        $this->secret = $secret;

        $this->tokenKey = $tokenKey;
    }

    /**
     * Find, parse and validate the JSON Web Token.
     *
     * @param ServerRequestInterface $request
     * @return Auth
     */
    public function authorise(ServerRequestInterface $request): Auth
    {
        try {
            $token = $this->getToken($request);
        } catch (ParseException $e) {
            return new Auth(400, 'Bad Request: ' . $e->getMessage());
        }

        return $this->validate($token);
    }

    /**
     * Check the token will parse, the signature is valid, the token is ready
     * to use, and it has not expired.
     *
     * @param string $token
     * @return Auth
     */
    private function validate(string $token): Auth
    {
        $jwt = new Jwt();

        $parse = $jwt->parser($token, $this->secret);

        $validate = new Validate($parse);

        $validationState = $validate->validate();

        $validationState = $validate->validateNotBefore($validationState);

        return $this->validationResponse(
            $validationState['code'],
            $validationState['message']
        );
    }

    /**
     * The authorisation process can respond as 200 Ok or 401 Unauthorized.
     *
     * @param int $code
     * @param string $message
     * @return Auth
     */
    private function validationResponse(int $code, string $message): Auth
    {
        if (in_array($code, [1, 2, 3, 4, 5], true)) {
            return new Auth(401, 'Unauthorized: ' . $message);
        }

        return new Auth(200, 'Ok');
    }

    /**
     * Find the token in the request. Ideally the token should be passed as
     * a bearer token in the authorization header. Passing the token via
     * query parameters is the least advisable option.
     *
     * @param ServerRequestInterface $request
     * @return string
     */
    private function getToken(ServerRequestInterface $request): string
    {
        $parseRequest = new Request(new Parse());

        return $parseRequest->parse($request, $this->tokenKey);
    }
}
