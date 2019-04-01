<?php

declare(strict_types=1);

namespace PsrJwt;

use PsrJwt\JwtAuthException;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

abstract class JwtAuth
{
    private $tokenKey;

    private $secret;

    public function __construct(string $tokenKey, string $secret)
    {
        $this->tokenKey = $tokenKey;

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
        return array_key_exists($this->tokenKey, $data);
    }

    protected function getToken(ServerRequestInterface $request): string
    {
        $merge = array_merge(
            $request->getServerParams(),
            $request->getCookieParams(),
            $request->getQueryParams(),
            $this->parseRequestBody($request)
        );

        if ($this->hasJwt($merge)) {
            return $merge[$this->tokenKey];
        }

        throw new JwtAuthException('JWT Token not set', 1);
    }

    protected function getSecret(): string
    {
        return $this->secret;
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
}
