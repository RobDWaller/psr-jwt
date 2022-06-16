<?php

declare(strict_types=1);

namespace PsrJwt\Location;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Find the JSON Web Token in the query parameters. This should only be used
 * in a low security situation, PII should not be associated with these
 * types of requests.
 */
class Query implements LocationInterface
{
    private string $tokenKey;

    public function __construct(string $tokenKey)
    {
        $this->tokenKey = $tokenKey;
    }

    public function find(ServerRequestInterface $request): string
    {
        return $request->getQueryParams()[$this->tokenKey] ?? '';
    }
}
