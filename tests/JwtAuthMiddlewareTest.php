<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Nyholm\Psr7\Factory\Psr17Factory;
use PsrJwt\Factory\Jwt;
use PsrJwt\JwtAuthMiddleware;
use PsrJwt\Auth\Authenticate;
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
        $authenticate = new Authenticate('jwt', 'secret');

        $process = new JwtAuthMiddleware($authenticate);

        $this->assertInstanceOf(JwtAuthMiddleware::class, $process);
        $this->assertInstanceOf(MiddlewareInterface::class, $process);
    }

    /**
     * @covers PsrJwt\JwtAuthMiddleware::process
     * @uses PsrJwt\Factory\Jwt
     * @uses PsrJwt\JwtAuthHandler
     * @uses PsrJwt\Parser\Parse
     * @uses PsrJwt\Validation\Validate
     * @uses PsrJwt\Parser\Bearer
     */
    public function testProcess()
    {
        $jwt = Jwt::builder();
        $token = $jwt->setSecret('Secret123!456$')
            ->setIssuer('localhost')
            ->setPayloadClaim('nbf', time() - 60)
            ->build()
            ->getToken();

        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getHeader')
            ->with('authorization')
            ->once()
            ->andReturn(['Bearer ' . $token]);

        $response = new Psr17Factory();
        $response = $response->createResponse(200, 'Ok');

        $handler = m::mock(RequestHandlerInterface::class);
        $handler->shouldReceive('handle')
            ->once()
            ->andReturn($response);

        $authenticate = new Authenticate('jwt', 'Secret123!456$');

        $process = new JwtAuthMiddleware($authenticate);

        $result = $process->process($request, $handler);

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertSame(200, $result->getStatusCode());
        $this->assertSame('Ok', $result->getReasonPhrase());
    }

    public function tearDown() {
        m::close();
    }
}
