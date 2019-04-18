<?php

declare(strict_types=1);

namespace PsrJwt;

use PsrJwt\Factory\Jwt;
use PsrJwt\Validation\Validate;
use PsrJwt\Parser\Parse;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReallySimpleJWT\Exception\ValidateException;
use Nyholm\Psr7\Factory\Psr17Factory;
use Throwable;

class JwtAuthHandler implements RequestHandlerInterface
{
    private $tokenKey;

    private $secret;

    public function __construct(string $tokenKey, string $secret)
    {
        $this->tokenKey = $tokenKey;

        $this->secret = $secret;
    }

    public function getSecret(): string
    {
        return $this->secret;
    }

    private function validate(string $token): ResponseInterface
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

    private function validationResponse(int $code, string $message): ResponseInterface
    {
        $factory = new Psr17Factory();

        if (in_array($code, [1, 2, 3, 4, 5], true)) {
            return $factory->createResponse(401, 'Unauthorized: ' . $message);
        }

        return $factory->createResponse(200, 'Ok');
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

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $token = $this->getToken($request);
        } catch (ValidateException $e) {
            $factory = new Psr17Factory();
            return $factory->createResponse(400, 'Bad Request: ' . $e->getMessage());
        }

        return $this->validate($token);
    }
}
