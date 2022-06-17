<?php

declare(strict_types=1);

namespace PsrJwt\Handler;

class Config
{
    private string $secret;

    private string $key;

    private string | array $response;

    public function __construct(string $secret, string $key, string | array $response)
    {
        $this->secret = $secret;

        $this->key = $key;

        $this->response = $response;
    }

    public function getSecret(): string
    {
        return $this->secret;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getResponse(): string | array
    {
        return $this->response;
    }
}