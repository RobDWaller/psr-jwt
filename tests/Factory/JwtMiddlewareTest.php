<?php

declare(strict_types=1);

namespace Tests\Factory;

use PHPUnit\Framework\TestCase;
use PsrJwt\Factory\JwtMiddleware;
use PsrJwt\JwtAuthMiddleware;
use PsrJwt\Factory\Jwt;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Nyholm\Psr7\Factory\Psr17Factory;

class JwtMiddlewareTest extends TestCase
{
    /**
     * @covers PsrJwt\Factory\JwtMiddleware::html
     * @uses PsrJwt\JwtAuthMiddleware::__construct
     * @uses PsrJwt\Auth\Authorise
     * @uses PsrJwt\Handler\Html
     */
    public function testJwtMiddlewareHtml(): void
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
    public function testFactoryValidation(): void
    {
        $jwt = $jwt = new Jwt();
        $jwt = $jwt->builder('Secret123!456$');
        $token = $jwt->setIssuer('localhost')
            ->setPayloadClaim('nbf', time() - 60)
            ->build()
            ->getToken();

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getHeader')
            ->with('authorization')
            ->willReturn(['Bearer ' . $token]);

        $response = new Psr17Factory();
        $response = $response->createResponse(200, 'Ok');

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())
            ->method('handle')
            ->willReturn($response);

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
    public function testJsonFactoryValidation(): void
    {
        $jwt = $jwt = new Jwt();
        $jwt = $jwt->builder('Secret123!456$');
        $token = $jwt->setIssuer('localhost')
            ->setPayloadClaim('nbf', time() - 60)
            ->build()
            ->getToken();

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getHeader')
            ->with('authorization')
            ->willReturn(['Bearer ' . $token]);

        $response = new Psr17Factory();
        $response = $response->createResponse(200, 'Ok');

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())
            ->method('handle')
            ->willReturn($response);

        $middleware = JwtMiddleware::json('Secret123!456$', 'jwt');

        $result = $middleware->process($request, $handler);

        $this->assertSame(200, $result->getStatusCode());
    }
}
