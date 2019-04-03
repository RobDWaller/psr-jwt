<?php

namespace Test;

use PHPUnit\Framework\TestCase;
use PsrJwt\JwtAuthHandler;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Mockery as m;

class JwtAuthHandlerTest extends TestCase
{
    public function testJwtAuthHandler()
    {
        $handler = new JwtAuthHandler();

        $this->assertInstanceOf(JwtAuthHandler::class, $handler);
        $this->assertInstanceOf(RequestHandlerInterface::class, $handler);
    }

    public function testJwtAuthHandlerResponse()
    {
        $request = m::mock(ServerRequestInterface::class);

        $handler = new JwtAuthHandler();

        $result = $handler->handle($request);

        $this->assertInstanceOf(ResponseInterface::class, $result);
    }
}
