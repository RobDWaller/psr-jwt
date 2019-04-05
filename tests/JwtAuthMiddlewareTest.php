<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use PsrJwt\JwtFactory;
use PsrJwt\JwtAuthMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Mockery as m;

class JwtAuthMiddlewareTest extends TestCase
{
    /**
     * @covers PsrJwt\JwtAuthMiddleware
     */
    public function testJwtAuthProcess()
    {
        $process = new JwtAuthMiddleware('jwt', 'secret');

        $this->assertInstanceOf(JwtAuthMiddleware::class, $process);
        $this->assertInstanceOf(MiddlewareInterface::class, $process);
    }

    /**
     * @covers PsrJwt\JwtAuthMiddleware::process
     * @uses PsrJwt\JwtFactory::builder
     */
    public function testProcess()
    {
        $jwt = JwtFactory::builder();
        $token = $jwt->setSecret('Secret123!456$')
            ->setIssuer('localhost')
            ->setPayloadClaim('nbf', time() - 60)
            ->build()
            ->getToken();

        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getServerParams')
            ->once()
            ->andReturn(['jwt' => $token]);
        $request->shouldReceive('getCookieParams')
            ->once()
            ->andReturn(['hello' => 'world']);
        $request->shouldReceive('getQueryParams')
            ->once()
            ->andReturn(['car' => 'park']);
        $request->shouldReceive('getParsedBody')
            ->twice()
            ->andReturn(['gary' => 'barlow']);
        $request->shouldReceive('getHeader')
            ->with('authorization')
            ->once()
            ->andReturn([]);

        $response = m::mock(ResponseInterface::class);

        $handler = m::mock(RequestHandlerInterface::class);
        $handler->shouldReceive('handle')
            ->once()
            ->andReturn($response);

        $process = new JwtAuthMiddleware('jwt', 'Secret123!456$');

        $result = $process->process($request, $handler);

        $this->assertInstanceOf(ResponseInterface::class, $result);
    }
}
