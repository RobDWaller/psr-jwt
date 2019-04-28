<?php

declare(strict_types=1);

namespace PsrJwt\Auth;

class Auth
{
    private $code;

    private $message;

    public function __construct(int $code, string $message)
    {
        $this->code = $code;

        $this->message = $message;
    }
}
