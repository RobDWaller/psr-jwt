<?php

declare(strict_types=1);

namespace PsrJwt;

use PsrJwt\JwtAuthException;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

abstract class JwtAuth
{
    private $secret;

    public function __construct(string $secret)
    {
        $this->secret = $secret;
    }

    protected function validate(string $token): bool
    {
        $parse = Jwt::parser($token, $this->getSecret());

        try {
            $parse->validate()
                ->validateExpiration();
        } catch (Throwable $e) {
            if (in_array($e->getCode(), [1, 2, 3, 4], true)) {
                throw new JwtAuthException($e->getMessage(), $e->getCode());
            }
        }

        try {
            $parse->validateNotBefore();
        } catch (Throwable $e) {
            if (in_array($e->getCode(), [5], true)) {
                throw new JwtAuthException($e->getMessage(), $e->getCode());
            }
        }

        return true;
    }

    protected function hasJwt(array $data): bool
    {
        return array_key_exists('jwt', $data);
    }

    protected function getToken(ServerRequestInterface $request): string
    {
        if ($this->hasJwt($request->getServerParams())) {
            return $request->getServerParams()['jwt'];
        }

        if ($this->hasJwt($request->getCookieParams())) {
            return $request->getCookieParams()['jwt'];
        }

        if ($this->hasJwt($request->getQueryParams())) {
            return $request->getQueryParams()['jwt'];
        }

        if ($this->hasJwt($this->parseRequestBody($request))) {
            return $this->parseRequestBody($request)['jwt'];
        }

        throw new JwtAuthException('JWT Token not set', 1);
    }

    protected function getSecret(): string
    {
        return $this->secret;
    }

    private function parseRequestBody(ServerRequestInterface $request): array
    {
        if (is_array($request->getParsedBody()) && isset($request->getParsedBody()['jwt'])) {
            return $request->getParsedBody();
        }

        if (is_object($request->getParsedBody()) && isset($request->getParsedBody()->jwt)) {
            return ['jwt' => $request->getParsedBody()->jwt];
        }

        return [];
    }
}
