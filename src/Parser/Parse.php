<?php

declare(strict_types=1);

namespace PsrJwt\Parser;

use PsrJwt\Parser\ParserInterface;
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
     * The JSON web token can be found in various parts of the request, a new
     * parser is required to search each part.
     *
     * @param ParserInterface $parser
     */
    public function addParser(ParserInterface $parser): void
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
            $token = $parser->parse($request);
            if (!empty($token)) {
                return $token;
            }
        }

        return '';
    }
}
