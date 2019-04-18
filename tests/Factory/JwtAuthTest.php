<?php

namespace Tests\Factory;

use PHPUnit\Framework\TestCase;
use PsrJwt\Factory\JwtAuth;
use PsrJwt\JwtAuthMiddleware;
use PsrJwt\JwtAuthHandler;
use PsrJwt\JwtAuthInvokable;

class JwtAuthTest extends TestCase
{
    /**
     * @covers PsrJwt\Factory\JwtAuth::middleware
     */
    public function testJwtAuthMiddleware()
    {
        $this->assertInstanceOf(
            JwtAuthMiddleware::class,
            JwtAuth::middleware()
        );
    }

    /**
     * @covers PsrJwt\Factory\JwtAuth::invokable
     * @uses PsrJwt\JwtAuthHandler::__construct
     * @uses PsrJwt\JwtAuthInvokable::__construct
     */
    public function testJwtAuthInvokable()
    {
        $this->assertInstanceOf(
            JwtAuthInvokable::class,
            JwtAuth::invokable('jwt', '$Secret123!')
        );
    }

    /**
     * @covers PsrJwt\Factory\JwtAuth::handler
     * @uses PsrJwt\JwtAuthHandler::__construct
     */
    public function testJwtAuthHandler()
    {
        $this->assertInstanceOf(
            JwtAuthHandler::class,
            JwtAuth::handler('jwt', '$Secret123!')
        );
    }
}
