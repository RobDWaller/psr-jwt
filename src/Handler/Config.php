<?php

declare(strict_types=1);

namespace PsrJwt\Handler;

class Config
{
    private string $secret;

    /**
     * @var string | mixed[] $response
     */
    private string | array $response;

    /**
     * @param string | mixed[] $response
     */
    public function __construct(string $secret, string | array $response)
    {
        $this->secret = $secret;

        $this->response = $response;
    }

    public function getSecret(): string
    {
        return $this->secret;
    }

    /**
     * @return string | mixed[]
     */
    public function getResponse(): string | array
    {
        return $this->response;
    }
}
