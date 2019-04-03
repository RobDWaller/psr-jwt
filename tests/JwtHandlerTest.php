<?php

namespace Test;

use PHPUnit\Framework\TestCase;
use PsrJwt\JwtHandler;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Mockery as m;

class JwtHandlerTest extends TestCase
{
    public function testJwtHandler()
    {
        $handler = new JwtHandler();

        $this->assertInstanceOf(JwtHandler::class, $handler);
        $this->assertInstanceOf(RequestHandlerInterface::class, $handler);
    }

    public function testJwtHandlerResponse()
    {
        $request = m::mock(ServerRequestInterface::class);

        $handler = new JwtHandler();

        $result = $handler->handle($request);

        $this->assertInstanceOf(ResponseInterface::class, $result);
    }
}
