<?php

namespace Tests\Factory;

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
        $jwt = new Jwt();
        $jwt = $jwt->builder();

        $this->assertInstanceOf(Build::class, $jwt);
    }

    /**
     * @covers PsrJwt\Factory\Jwt::parser
     */
    public function testJwtParser()
    {
        $jwt = new Jwt();
        $jwt = $jwt->parser('aaa.bbb.ccc', 'secret');

        $this->assertInstanceOf(Parse::class, $jwt);
    }
}
