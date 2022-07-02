<?php

declare(strict_types=1);

namespace PsrJwt\Auth;

use PsrJwt\Status\Status;

interface AuthoriseInterface
{
    public function authorise(string $token, string $secret): Status;
}
