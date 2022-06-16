<?php

declare(strict_types=1);

namespace PsrJwt\Location;

use Psr\Http\Message\ServerRequestInterface;

/**
 * The parse method is used to retrieve the JWT token from the request object.
 */
interface LocationInterface
{
    public function find(ServerRequestInterface $request): string;
}
