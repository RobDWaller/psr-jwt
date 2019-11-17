<?php

declare(strict_types=1);

namespace PsrJwt\Parser;

use PsrJwt\Parser\ParserInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Find the JSON Web Token in the Body of the request. Not an ideal way to pass
 * around JWTs but if it's a low security situation probably fine.
 */
class Body implements ParserInterface
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
     * The parsed body information can be returned as an array or an object.
     *
     * @param ServerRequestInterface $request
     * @return string
     */
    public function parse(ServerRequestInterface $request): string
    {
        $body = $request->getParsedBody();

        if (is_array($body) && isset($body[$this->tokenKey])) {
            return $body[$this->tokenKey];
        }

        return $this->parseBodyObject($request);
    }

    /**
     * If the body information is not returned as an array see if it is
     * returned as an object.
     *
     * @param ServerRequestInterface $request
     * @return string
     */
    private function parseBodyObject(ServerRequestInterface $request): string
    {
        $body = $request->getParsedBody();

        if (is_object($body) && isset($body->{$this->tokenKey})) {
            return $body->{$this->tokenKey};
        }

        return '';
    }
}
