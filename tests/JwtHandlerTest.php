<?php

namespace Test;

use PHPUnit\Framework\TestCase;
use PsrJwt\JwtHandler;
use Psr\Http\Server\RequestHandlerInterface;

class JwtHandlerTest extends TestCase
{
    public function testJwtHandler()
    {
        $handler = new JwtHandler();

        $this->assertInstanceOf(JwtHandler::class, $handler);
        $this->assertInstanceOf(RequestHandlerInterface::class, $handler);
    }
}
