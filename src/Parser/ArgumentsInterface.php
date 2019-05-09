<?php

namespace PsrJwt\Parser;

use PsrJwt\Parser\ParserInterface;

/**
 * Some parsers require arguments passed in via the constructor to execute
 * properly.
 */
interface ArgumentsInterface extends ParserInterface
{
    /**
     * @param array $arguments
     */
    public function __construct(array $arguments);
}
