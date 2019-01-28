<?php

namespace Test;

use PHPUnit\Framework\TestCase;
use ReallySimpleJWT\Build;
use ReallySimpleJWT\Parse;
use PsrJwt\Jwt;

class JwtTest extends TestCase
{
    /**
     * @covers PsrJwt\Jwt::builder
     */
    public function testJwtBuilder()
    {
        $this->assertInstanceOf(Build::class, Jwt::builder());
    }

    /**
     * @covers PsrJwt\Jwt::parser
     */
    public function testJwtParser()
    {
        $this->assertInstanceOf(Parse::class, Jwt::parser('aaa.bbb.ccc', 'secret'));
    }
}
