<?php

declare(strict_types=1);

namespace Tests\Parser;

use PHPUnit\Framework\TestCase;
use PsrJwt\Location\LocationException;
use Exception;

class LocationExceptionTest extends TestCase
{
    /**
     * @covers PsrJwt\Location\LocationException::__construct
     */
    public function testLocationException(): void
    {
        $exception = new LocationException('Error', 1, null);

        $this->assertInstanceOf(LocationException::class, $exception);
        $this->assertInstanceOf(Exception::class, $exception);
        $this->assertSame('Error', $exception->getMessage());
        $this->assertSame(1, $exception->getCode());
    }

    /**
     * @covers PsrJwt\Location\LocationException::__construct
     */
    public function testLocationExceptionDefault(): void
    {
        $exception = new LocationException('Error');

        $this->assertInstanceOf(LocationException::class, $exception);
        $this->assertInstanceOf(Exception::class, $exception);
        $this->assertSame('Error', $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
    }
}
