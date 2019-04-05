<?php

namespace Test;

use PHPUnit\Framework\TestCase;
use PsrJwt\JwtAuthFactory;
use PsrJwt\JwtAuthMiddleware;

class JwtAuthFactoryTest extends TestCase
{
    /**
     * @covers PsrJwt\JwtAuthFactory::Middleware
     */
    public function testJwtAuthFactoryMiddleware()
    {
        $this->assertInstanceOf(
            JwtAuthMiddleware::class,
            JwtAuthFactory::middleware()
        );
    }
}
