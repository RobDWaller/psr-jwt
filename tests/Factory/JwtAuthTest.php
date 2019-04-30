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
}
