<?php

declare(strict_types = 1);

namespace PsrJwt\Parser;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Find the JSON Web Token within the incoming request object.
 */
class Parse
{
    /**
     * @var array $parsers
     */
    private $parsers = [];

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
     * The JSON web token can be found in various parts of the request, a new
     * parser is required to search each part.
     *
     * @param string
     */
    public function addParser(string $parser): void
    {
        $this->parsers[] = $parser;
    }

    /**
     * Parsers are only instantiated if the JWT can't be found.
     *
     * @param ServerRequestInterface $request
     * @return string
     */
    public function findToken(ServerRequestInterface $request): string
    {
        foreach ($this->parsers as $parser) {
            $object = new $parser($this->arguments);
            $token = $object->parse($request);
            if (!empty($token)) {
                return $token;
            }
        }

        return '';
    }
}
