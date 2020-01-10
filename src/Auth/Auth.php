<?php

declare(strict_types=1);

namespace PsrJwt\Auth;

/**
 * Tell the middleware what the status code and reason phrase are when the JWT
 * authorisation process is complete.
 */
class Auth
{
    /**
     * @var int $code
     */
    private $code;

    /**
     * @var string $message
     */
    private $message;

    /**
     * @param int $code
     * @param string $message
     */
    public function __construct(int $code, string $message)
    {
        $this->code = $code;

        $this->message = $message;
    }

    /**
     * Return the status code based on token authorisation.
     *
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * Return the reason phrase based on token authorisation.
     *
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}
