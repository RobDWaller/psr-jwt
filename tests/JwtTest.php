<?php

namespace Test;

use PHPUnit\Framework\TestCase;
use ReallySimpleJWT\Build;
use ReallySimpleJWT\Parse;
use PsrJwt\Jwt;

class JwtTest extends TestCase
{
    public function testJwtBuilder()
    {
        $this->assertInstanceOf(Build::class, Jwt::builder());
    }

    public function testJwtParser()
    {
        $this->assertInstanceOf(Parse::class, Jwt::parser('aaa.bbb.ccc', 'secret'));
    }
}
