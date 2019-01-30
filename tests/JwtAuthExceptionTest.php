<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use PsrJwt\JwtAuthException;

class JwtAuthExceptionTest extends TestCase
{
    /**
     * @covers PsrJwt\JwtAuthException::__construct
     */
    public function testJwtAuthException()
    {
        $exception = new JwtAuthException('Error!', 1);

        $this->assertInstanceOf(JwtAuthException::class, $exception);
    }
}
