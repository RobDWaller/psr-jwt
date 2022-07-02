<?php

declare(strict_types=1);

namespace PsrJwt\Handler;

class Config
{
    private string $secret;

    private string | array $response;

    public function __construct(string $secret, string | array $response)
    {
        $this->secret = $secret;

        $this->response = $response;
    }

    public function getSecret(): string
    {
        return $this->secret;
    }

    public function getResponse(): string | array
    {
        return $this->response;
    }
}
