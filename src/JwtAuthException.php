<?php

declare(strict_types=1);

namespace PsrJwt;

use Exception;
use Throwable;

/**
 * Simple PHP exception extension class for all JWT auth exceptions to
 * make exceptions more specific and obvious.
 *
 * @author Rob Waller <rdwaller1984@gmail.com>
 */
class JwtAuthException extends Exception
{
    /**
     * Constructor for the Jwt Auth Exception class
     *
     * @param string $message
     * @param int $code
     * @param Throwable $previous
     */
    public function __construct(string $message, int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
