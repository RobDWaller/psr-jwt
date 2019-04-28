<?php

namespace Tests\Factory;

use PHPUnit\Framework\TestCase;
use PsrJwt\Factory\JwtAuth;
use PsrJwt\JwtAuthMiddleware;
use PsrJwt\Auth\Authenticate;
use PsrJwt\JwtAuthInvokable;

class JwtAuthTest extends TestCase
{
    /**
     * @covers PsrJwt\Factory\JwtAuth::middleware
     * @uses PsrJwt\JwtAuthMiddleware::__construct
     * @uses PsrJwt\Auth\Authenticate
     */
    public function testJwtAuthMiddleware()
    {
        $this->assertInstanceOf(
            JwtAuthMiddleware::class,
            JwtAuth::middleware('jwt', '$Secret123!')
        );
    }

    /**
     * @covers PsrJwt\Factory\JwtAuth::invokable
     * @uses PsrJwt\JwtAuthInvokable::__construct
     * @uses PsrJwt\Auth\Authenticate
     */
    public function testJwtAuthInvokable()
    {
        $this->assertInstanceOf(
            JwtAuthInvokable::class,
            JwtAuth::invokable('jwt', '$Secret123!')
        );
    }
}
