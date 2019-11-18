<?php

namespace Tests\Parser;

use PHPUnit\Framework\TestCase;
use PsrJwt\Parser\ParseException;

class ParseExceptionTest extends TestCase
{
    public function testParseException()
    {
        $exception = new ParseException('Error', 1, null);

        $this->assertInstanceOf(ParseException::class, $exception);
    }
}