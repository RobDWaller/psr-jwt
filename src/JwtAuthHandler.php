<?php

declare(strict_types=1);

namespace PsrJwt;

use PsrJwt\JwtFactory;
use PsrJwt\JwtValidate;
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

    protected function getSecret(): string
    {
        return $this->secret;
    }

    protected function validate(string $token): ResponseInterface
    {
        $parse = JwtFactory::parser($token, $this->getSecret());

        $validate = new JwtValidate($parse);

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

    protected function hasJwt(array $data): bool
    {
        return array_key_exists($this->tokenKey, $data);
    }

    protected function getToken(ServerRequestInterface $request): string
    {
        $merge = array_merge(
            $request->getServerParams(),
            $request->getCookieParams(),
            $request->getQueryParams(),
            $this->parseRequestBody($request),
            $this->parseBearerToken($request)
        );

        if ($this->hasJwt($merge)) {
            return $merge[$this->tokenKey];
        }

        throw new ValidateException('JSON Web Token not set.', 11);
    }

    private function parseRequestBody(ServerRequestInterface $request): array
    {
        if (is_array($request->getParsedBody()) && isset($request->getParsedBody()[$this->tokenKey])) {
            return $request->getParsedBody();
        }

        if (is_object($request->getParsedBody()) && isset($request->getParsedBody()->jwt)) {
            return [$this->tokenKey => $request->getParsedBody()->jwt];
        }

        return [];
    }

    private function parseBearerToken(ServerRequestInterface $request): array
    {
        $token = $this->getBearerToken($request);

        return !empty($token) ? [$this->tokenKey => $token] : [];
    }

    private function getBearerToken(ServerRequestInterface $request): string
    {
        $authorization = $request->getHeader('authorization');

        $bearer = array_filter($authorization, function ($item) {
            return (bool) preg_match('/^bearer\s.+/', $item);
        });

        return explode(' ', $bearer[0] ?? '')[1] ?? '';
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
