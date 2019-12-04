<?php

namespace Tests\Factory;

use PHPUnit\Framework\TestCase;
use PsrJwt\Factory\JwtMiddleware;
use PsrJwt\JwtAuthMiddleware;
use PsrJwt\Auth\Authorise;
use PsrJwt\Factory\Jwt;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use Mockery as m;

class JwtMiddlewareTest extends TestCase
{
    /**
     * @covers PsrJwt\Factory\JwtMiddleware::html
     * @uses PsrJwt\JwtAuthMiddleware::__construct
     * @uses PsrJwt\Auth\Authorise
     * @uses PsrJwt\Handler\Html
     */
    public function testJwtMiddlewareHtml()
    {
        $this->assertInstanceOf(
            JwtAuthMiddleware::class,
            JwtMiddleware::html('$Secret123!', 'jwt')
        );
    }

    /**
     * @covers PsrJwt\JwtAuthMiddleware::process
     * @uses PsrJwt\Factory\JwtMiddleware
     * @uses PsrJwt\Auth\Auth
     * @uses PsrJwt\Auth\Authorise
     * @uses PsrJwt\Handler\Html
     * @uses PsrJwt\Factory\Jwt
     * @uses PsrJwt\JwtAuthMiddleware
     * @uses PsrJwt\Parser\Bearer
     * @uses PsrJwt\Parser\Parse
     * @uses PsrJwt\Validation\Validate
     * @uses PsrJwt\Parser\Body
     * @uses PsrJwt\Parser\Cookie
     * @uses PsrJwt\Parser\Query
     * @uses PsrJwt\Parser\Request
     */
    public function testFactoryValidation()
    {
        $jwt = $jwt = new Jwt();
        $jwt = $jwt->builder();
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

        $middleware = JwtMiddleware::html('Secret123!456$', 'jwt');

        $result = $middleware->process($request, $handler);

        $this->assertSame(200, $result->getStatusCode());
    }

    /**
     * @covers PsrJwt\Factory\JwtMiddleware::json
     * @uses PsrJwt\Auth\Auth
     * @uses PsrJwt\Auth\Authorise
     * @uses PsrJwt\Handler\Json
     * @uses PsrJwt\Factory\Jwt
     * @uses PsrJwt\JwtAuthMiddleware
     * @uses PsrJwt\Parser\Bearer
     * @uses PsrJwt\Parser\Parse
     * @uses PsrJwt\Validation\Validate
     * @uses PsrJwt\Parser\Body
     * @uses PsrJwt\Parser\Cookie
     * @uses PsrJwt\Parser\Query
     * @uses PsrJwt\Parser\Request
     */
    public function testJsonFactoryValidation()
    {
        $jwt = $jwt = new Jwt();
        $jwt = $jwt->builder();
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

        $middleware = JwtMiddleware::json('Secret123!456$', 'jwt');

        $result = $middleware->process($request, $handler);

        $this->assertSame(200, $result->getStatusCode());
    }
}
