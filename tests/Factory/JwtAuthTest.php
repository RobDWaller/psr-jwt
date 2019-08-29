<?php

namespace Tests\Factory;

use PHPUnit\Framework\TestCase;
use PsrJwt\Factory\JwtAuth;
use PsrJwt\JwtAuthMiddleware;
use PsrJwt\Auth\Authenticate;
use PsrJwt\Factory\Jwt;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use Mockery as m;

class JwtAuthTest extends TestCase
{
    /**
     * @covers PsrJwt\Factory\JwtAuth::middleware
     * @uses PsrJwt\JwtAuthMiddleware::__construct
     * @uses PsrJwt\Auth\Authenticate
     * @uses PsrJwt\Handler\Html
     */
    public function testJwtAuthMiddleware()
    {
        $this->assertInstanceOf(
            JwtAuthMiddleware::class,
            JwtAuth::middleware('$Secret123!', 'jwt')
        );
    }

    /**
     * @covers PsrJwt\JwtAuthMiddleware::process
     * @uses PsrJwt\Factory\JwtAuth
     * @uses PsrJwt\Auth\Auth
     * @uses PsrJwt\Auth\Authenticate
     * @uses PsrJwt\Handler\Html
     * @uses PsrJwt\Factory\Jwt
     * @uses PsrJwt\JwtAuthMiddleware
     * @uses PsrJwt\Parser\Bearer
     * @uses PsrJwt\Parser\Parse
     * @uses PsrJwt\Validation\Validate
     */
    public function testFactoryValidation()
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

        $middleware = JwtAuth::middleware('Secret123!456$', 'jwt');

        $result = $middleware->process($request, $handler);

        $this->assertSame(200, $result->getStatusCode());
    }

    /**
     * @covers PsrJwt\JwtAuthMiddleware::process
     * @uses PsrJwt\Factory\JwtAuth
     * @uses PsrJwt\Auth\Auth
     * @uses PsrJwt\Auth\Authenticate
     * @uses PsrJwt\Handler\Json
     * @uses PsrJwt\Factory\Jwt
     * @uses PsrJwt\JwtAuthMiddleware
     * @uses PsrJwt\Parser\Bearer
     * @uses PsrJwt\Parser\Parse
     * @uses PsrJwt\Validation\Validate
     */
    public function testJsonFactoryValidation()
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

        $middleware = JwtAuth::json('Secret123!456$', 'jwt');

        $result = $middleware->process($request, $handler);

        $this->assertSame(200, $result->getStatusCode());
    }
}
