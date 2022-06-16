<?php

declare(strict_types=1);

namespace PsrJwt\Location;

use Exception;
use Throwable;

/**
 * Simple PHP exception extension class for all request parse exceptions to
 * make exceptions more specific and obvious.
 */
class LocationException extends Exception
{
    public function __construct(string $message, int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
