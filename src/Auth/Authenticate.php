<?php

declare(strict_types=1);

namespace PsrJwt\Auth;

class Authenticate
{
    private $tokenKey;

    private $secret;

    public function __construct(string $tokenKey, string $secret)
    {
        $this->tokenKey = $tokenKey;

        $this->secret = $secret;
    }
}
