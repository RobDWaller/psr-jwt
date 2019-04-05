<?php

namespace Test;

use PHPUnit\Framework\TestCase;
use PsrJwt\JwtAuthFactory;
use PsrJwt\JwtAuthMiddleware;
use PsrJwt\JwtAuthHandler;
use PsrJwt\JwtAuthInvokable;

class JwtAuthFactoryTest extends TestCase
{
    /**
     * @covers PsrJwt\JwtAuthFactory::middleware
     */
    public function testJwtAuthFactoryMiddleware()
    {
        $this->assertInstanceOf(
            JwtAuthMiddleware::class,
            JwtAuthFactory::middleware()
        );
    }

    /**
     * @covers PsrJwt\JwtAuthFactory::invokable
     */
    public function testJwtAuthFactoryInvokable()
    {
        $this->assertInstanceOf(
            JwtAuthInvokable::class,
            JwtAuthFactory::invokable('jwt', '$Secret123!')
        );
    }

    /**
     * @covers PsrJwt\JwtAuthFactory::handler
     */
    public function testJwtAuthFactoryHandler()
    {
        $this->assertInstanceOf(
            JwtAuthHandler::class,
            JwtAuthFactory::handler('jwt', '$Secret123!')
        );
    }
}
