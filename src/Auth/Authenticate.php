<?php

declare(strict_types=1);

namespace PsrJwt\Auth;

use Psr\Http\Message\ServerRequestInterface;
use ReallySimpleJWT\Exception\ValidateException;
use PsrJwt\Factory\Jwt;
use PsrJwt\Auth\Auth;
use PsrJwt\Parser\Parse;
use PsrJwt\Validation\Validate;

/**
 * Retrieve the JSON Web Token from the request and attempt to parse and
 * validate it.
 */
class Authenticate
{
    /**
     * Define under what key the JWT can be found in the request.
     *
     * @var string $tokenKey
     */
    private $tokenKey;

    /**
     * The secret required to parse and validate the JWT.
     *
     * @var string $secret
     */
    private $secret;

    /**
     * @param string $tokenKey
     * @param string $secret
     * @todo the tokenKey and secret are the wrong way around, secret is
     * required token key is not.
     */
    public function __construct(string $tokenKey, string $secret)
    {
        $this->tokenKey = $tokenKey;

        $this->secret = $secret;
    }

    /**
     * Find, parse and validate the JSON Web Token.
     *
     * @param ServerRequestInterface $request
     * @return Auth
     */
    public function authenticate(ServerRequestInterface $request): Auth
    {
        try {
            $token = $this->getToken($request);
        } catch (ValidateException $e) {
            return new Auth(400, 'Bad Request: ' . $e->getMessage());
        }

        return $this->validate($token);
    }

    /**
     * Check the token will parse, the signature is valid, it is ready to use
     * and it has not expired.
     *
     * @param string $token
     * @return Auth
     */
    private function validate(string $token): Auth
    {
        $parse = Jwt::parser($token, $this->secret);

        $validate = new Validate($parse);

        $validationState = $validate->validate();

        $validationState = $validate->validateNotBefore($validationState);

        return $this->validationResponse(
            $validationState['code'],
            $validationState['message']
        );
    }

    /**
     * The authentication can respond as Ok or Unauthorized.
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
     * The token found in the request should not be empty.
     *
     * @param string $token
     * @return bool
     */
    private function hasJwt(string $token): bool
    {
        return !empty($token);
    }

    /**
     * Find the token in the request. Ideally the token should be passed as
     * a bearer token in the authorization header. Passing the token via
     * query parameters is the least advisable option.
     *
     * @param ServerRequestInterface $request
     * @return string
     * @throws ValidateException
     */
    private function getToken(ServerRequestInterface $request): string
    {
        $parse = new Parse(['token_key' => $this->tokenKey]);
        $parse->addParser(\PsrJwt\Parser\Bearer::class);
        $parse->addParser(\PsrJwt\Parser\Cookie::class);
        $parse->addParser(\PsrJwt\Parser\Body::class);
        $parse->addParser(\PsrJwt\Parser\Query::class);

        $token = $parse->findToken($request);

        if ($this->hasJwt($token)) {
            return $token;
        }

        throw new ValidateException('JSON Web Token not set.', 11);
    }
}
