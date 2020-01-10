<?php

namespace Tests\Parser;

use PHPUnit\Framework\TestCase;
use PsrJwt\Parser\ParseException;
use Exception;

class ParseExceptionTest extends TestCase
{
    /**
     * @covers PsrJwt\Parser\ParseException
     */
    public function testParseException()
    {
        $exception = new ParseException('Error', 1, null);

        $this->assertInstanceOf(ParseException::class, $exception);
        $this->assertInstanceOf(Exception::class, $exception);
    }
}
