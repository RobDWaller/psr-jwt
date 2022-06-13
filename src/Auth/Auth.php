<?php

declare(strict_types=1);

namespace PsrJwt\Auth;

/**
 * Tell the middleware what the status code and reason phrase are when the JWT
 * authorisation process is complete.
 */
class Auth
{
    private int $code;

    private string $message;

    public function __construct(int $code, string $message)
    {
        $this->code = $code;

        $this->message = $message;
    }

    /**
     * Return the status code based on token authorisation.
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * Return the reason phrase based on token authorisation.
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}
