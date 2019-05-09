<?php

declare(strict_types=1);

namespace PsrJwt\Parser;

use PsrJwt\Parser\ArgumentsInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Find the JSON Web Token in the query parameters. This should only be used
 * in a low security situation, PII should not be associated with these
 * types of requests.
 */
class Query implements ArgumentsInterface
{
    /**
     * @var array $arguments
     */
    private $arguments;

    /**
     * @param array $arguments
     */
    public function __construct(array $arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * @param ServerRequestInterface $request
     * @return string
     */
    public function parse(ServerRequestInterface $request): string
    {
        return $request->getQueryParams()[$this->arguments['token_key']] ?? '';
    }
}
