<?php

namespace Tests\Factory;

use PHPUnit\Framework\TestCase;
use ReallySimpleJWT\Build;
use ReallySimpleJWT\Parse;
use ReallySimpleJWT\Validate;
use PsrJwt\Factory\Jwt;

class JwtTest extends TestCase
{
    /**
     * @covers PsrJwt\Factory\Jwt::builder
     */
    public function testJwtBuilder(): void
    {
        $jwt = new Jwt();
        $jwt = $jwt->builder();

        $this->assertInstanceOf(Build::class, $jwt);
    }

    /**
     * @covers PsrJwt\Factory\Jwt::parser
     */
    public function testJwtParser(): void
    {
        $jwt = new Jwt();
        $jwt = $jwt->parser('aaa.bbb.ccc', 'secret');

        $this->assertInstanceOf(Parse::class, $jwt);
    }

    /**
     * @covers PsrJwt\Factory\Jwt::validator
     */
    public function testJwtValidator(): void
    {
        $jwt = new Jwt();
        $jwt = $jwt->validator('aaa.bbb.ccc', 'secret');

        $this->assertInstanceOf(Validate::class, $jwt);
    }
}
