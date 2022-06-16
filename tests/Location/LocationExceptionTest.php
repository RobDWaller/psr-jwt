<?php

declare(strict_types=1);

namespace Tests\Parser;

use PHPUnit\Framework\TestCase;
use PsrJwt\Location\LocationException;
use Exception;

class LocationExceptionTest extends TestCase
{
    /**
     * @covers PsrJwt\Parser\ParseException
     */
    public function testLocationException(): void
    {
        $exception = new LocationException('Error', 1, null);

        $this->assertInstanceOf(LocationException::class, $exception);
        $this->assertInstanceOf(Exception::class, $exception);
    }
}
