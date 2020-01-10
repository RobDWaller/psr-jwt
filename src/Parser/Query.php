<?php

declare(strict_types=1);

namespace PsrJwt\Parser;

use PsrJwt\Parser\ParserInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Find the JSON Web Token in the query parameters. This should only be used
 * in a low security situation, PII should not be associated with these
 * types of requests.
 */
class Query implements ParserInterface
{
    /**
     * @var string $tokenKey
     */
    private $tokenKey;

    /**
     * @param string $tokenKey
     */
    public function __construct(string $tokenKey)
    {
        $this->tokenKey = $tokenKey;
    }

    /**
     * @param ServerRequestInterface $request
     * @return string
     */
    public function parse(ServerRequestInterface $request): string
    {
        return $request->getQueryParams()[$this->tokenKey] ?? '';
    }
}
