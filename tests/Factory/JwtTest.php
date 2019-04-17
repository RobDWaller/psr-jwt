<?php

namespace Test\Factory;

use PHPUnit\Framework\TestCase;
use ReallySimpleJWT\Build;
use ReallySimpleJWT\Parse;
use PsrJwt\Factory\Jwt;

class JwtTest extends TestCase
{
    /**
     * @covers PsrJwt\Factory\Jwt::builder
     */
    public function testJwtBuilder()
    {
        $this->assertInstanceOf(Build::class, Jwt::builder());
    }

    /**
     * @covers PsrJwt\Factory\Jwt::parser
     */
    public function testJwtParser()
    {
        $this->assertInstanceOf(Parse::class, Jwt::parser('aaa.bbb.ccc', 'secret'));
    }
}
