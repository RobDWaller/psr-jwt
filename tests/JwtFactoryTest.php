<?php

namespace Test;

use PHPUnit\Framework\TestCase;
use ReallySimpleJWT\Build;
use ReallySimpleJWT\Parse;
use PsrJwt\JwtFactory;

class JwtFactoryTest extends TestCase
{
    /**
     * @covers PsrJwt\Jwt::builder
     */
    public function testJwtFactoryBuilder()
    {
        $this->assertInstanceOf(Build::class, JwtFactory::builder());
    }

    /**
     * @covers PsrJwt\Jwt::parser
     */
    public function testJwtFactoryParser()
    {
        $this->assertInstanceOf(Parse::class, JwtFactory::parser('aaa.bbb.ccc', 'secret'));
    }
}
