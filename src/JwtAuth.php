<?php

declare(strict_types=1);

namespace PsrJwt;

use PsrJwt\JwtAuthException;
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
            $parse->validate();
        }
        catch (Throwable $e) {
            throw new JwtAuthException('Fail!!');
        }

        return true;
    }

    protected function hasJwt(array $data): bool
    {
        return array_key_exists('jwt', $data);
    }

    protected function getToken(array $server, array $cookie, array $query, array $body): string
    {
        if ($this->hasJwt($server)) {
            return $server['jwt'];
        }

        if ($this->hasJwt($cookie)) {
            return $cookie['jwt'];
        }

        if ($this->hasJwt($query)) {
            return $query['jwt'];
        }

        if ($this->hasJwt($body)) {
            return $body['jwt'];
        }

        throw new JwtAuthException('JWT Token not set', 1);
    }

    protected function getSecret(): string
    {
        return $this->secret;
    }
}
