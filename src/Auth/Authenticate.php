<?php

declare(strict_types=1);

namespace PsrJwt\Auth;

use Psr\Http\Message\ServerRequestInterface;
use ReallySimpleJWT\Exception\ValidateException;
use PsrJwt\Factory\Jwt;
use PsrJwt\Auth\Auth;
use PsrJwt\Parser\Parse;
use PsrJwt\Validation\Validate;

class Authenticate
{
    private $tokenKey;

    private $secret;

    public function __construct(string $tokenKey, string $secret)
    {
        $this->tokenKey = $tokenKey;

        $this->secret = $secret;
    }

    public function authenticate(ServerRequestInterface $request): Auth
    {
        try {
            $token = $this->getToken($request);
        } catch (ValidateException $e) {
            return new Auth(400, 'Bad Request: ' . $e->getMessage());
        }

        return $this->validate($token);
    }

    public function getSecret(): string
    {
        return $this->secret;
    }

    private function validate(string $token): Auth
    {
        $parse = Jwt::parser($token, $this->getSecret());

        $validate = new Validate($parse);

        $validationState = $validate->validate();

        $validationState = $validate->validateNotBefore($validationState);

        return $this->validationResponse(
            $validationState['code'],
            $validationState['message']
        );
    }

    private function validationResponse(int $code, string $message): Auth
    {
        if (in_array($code, [1, 2, 3, 4, 5], true)) {
            return new Auth(401, 'Unauthorized: ' . $message);
        }

        return new Auth(200, 'Ok');
    }

    private function hasJwt(string $token): bool
    {
        return !empty($token);
    }

    private function getToken(ServerRequestInterface $request): string
    {
        $parse = new Parse(['token_key' => $this->tokenKey]);
        $parse->addParser(\PsrJwt\Parser\Bearer::class);
        $parse->addParser(\PsrJwt\Parser\Cookie::class);
        $parse->addParser(\PsrJwt\Parser\Body::class);
        $parse->addParser(\PsrJwt\Parser\Query::class);
        $parse->addParser(\PsrJwt\Parser\Server::class);

        $token = $parse->findToken($request);

        if ($this->hasJwt($token)) {
            return $token;
        }

        throw new ValidateException('JSON Web Token not set.', 11);
    }
}
