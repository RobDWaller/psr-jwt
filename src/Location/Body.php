<?php

declare(strict_types=1);

namespace PsrJwt\Location;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Find the JSON Web Token in the Body of the request. Not an ideal way to pass
 * around JWTs but if it's a low security situation probably fine.
 */
class Body implements LocationInterface
{
    private string $tokenKey;

    public function __construct(string $tokenKey)
    {
        $this->tokenKey = $tokenKey;
    }

    /**
     * The parsed body information can be returned as an array or an object.
     */
    public function find(ServerRequestInterface $request): string
    {
        $body = $request->getParsedBody();

        if (is_array($body) && isset($body[$this->tokenKey])) {
            return $body[$this->tokenKey];
        }

        if (is_object($body) && isset($body->{$this->tokenKey})) {
            return $body->{$this->tokenKey};
        }

        return '';
    }
}