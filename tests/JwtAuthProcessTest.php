<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use PsrJwt\Jwt;
use PsrJwt\JwtAuthProcess;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Mockery as m;

class JwtAuthProcessTest extends TestCase
{
    /**
     * @covers PsrJwt\JwtAuthProcess
     */
    public function testJwtAuthProcess()
    {
        $process = new JwtAuthProcess('secret');

        $this->assertInstanceOf(JwtAuthProcess::class, $process);
        $this->assertInstanceOf(MiddlewareInterface::class, $process);
    }

    public function testProcess()
    {
        $jwt = Jwt::builder();
        $token = $jwt->setSecret('Secret123!456$')
            ->setIssuer('localhost')
            ->setPayloadClaim('nbf', time() - 60)
            ->build()
            ->getToken();

        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getServerParams')
            ->twice()
            ->andReturn(['jwt' => $token]);

        $response = m::mock(ResponseInterface::class);

        $handler = m::mock(RequestHandlerInterface::class);
        $handler->shouldReceive('handle')
            ->once()
            ->andReturn($response);

        $process = new JwtAuthProcess('Secret123!456$');

        $result = $process->process($request, $handler);

        $this->assertInstanceOf(ResponseInterface::class, $result);
    }
}
