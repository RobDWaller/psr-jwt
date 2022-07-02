<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Nyholm\Psr7\Factory\Psr17Factory;
use PsrJwt\Factory\Jwt;
use PsrJwt\JwtAuthMiddleware;
use PsrJwt\Handler\Html;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use PsrJwt\Factory\Handler;

class JwtAuthMiddlewareTest extends TestCase
{
    /**
     * @covers PsrJwt\JwtAuthMiddleware
     * @uses PsrJwt\Auth\Authorise
     * @uses PsrJwt\Handler\Html
     */
    public function testJwtAuthProcess(): void
    {
        $handler = Handler::html('secret', 'jwt', '');

        $process = new JwtAuthMiddleware($handler);

        $this->assertInstanceOf(JwtAuthMiddleware::class, $process);
        $this->assertInstanceOf(MiddlewareInterface::class, $process);
    }

    /**
     * @covers PsrJwt\JwtAuthMiddleware::process
     * @uses PsrJwt\JwtAuthMiddleware::__construct
     * @uses PsrJwt\Auth\Authorise
     * @uses PsrJwt\Auth\Auth
     * @uses PsrJwt\Factory\Jwt
     * @uses PsrJwt\Parser\Parse
     * @uses PsrJwt\Validation\Validate
     * @uses PsrJwt\Parser\Bearer
     * @uses PsrJwt\Handler\Html
     * @uses PsrJwt\Parser\Body
     * @uses PsrJwt\Parser\Cookie
     * @uses PsrJwt\Parser\Query
     * @uses PsrJwt\Parser\Request
     */
    public function testProcess(): void
    {
        $jwt = $jwt = new Jwt();
        $jwt = $jwt->builder('Secret123!456$');
        $token = $jwt->setIssuer('localhost')
            ->setExpiration(time() + 10)
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

        $html = Handler::html('', 'Secret123!456$', '');

        $process = new JwtAuthMiddleware($html);

        $result = $process->process($request, $handler);

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertSame(200, $result->getStatusCode());
        $this->assertSame('Ok', $result->getReasonPhrase());
    }

    /**
     * @covers PsrJwt\JwtAuthMiddleware::process
     * @uses PsrJwt\JwtAuthMiddleware
     * @uses PsrJwt\Auth\Authorise
     * @uses PsrJwt\Auth\Auth
     * @uses PsrJwt\Factory\Jwt
     * @uses PsrJwt\Parser\Parse
     * @uses PsrJwt\Validation\Validate
     * @uses PsrJwt\Parser\Bearer
     * @uses PsrJwt\Parser\Body
     * @uses PsrJwt\Parser\Cookie
     * @uses PsrJwt\Parser\Query
     * @uses PsrJwt\Handler\Html
     * @uses PsrJwt\Parser\Request
     * @uses PsrJwt\Parser\ParseException
     */
    public function testProcessFail(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getCookieParams')
            ->willReturn(['car' => 'park']);
        $request->expects($this->once())
            ->method('getQueryParams')
            ->willReturn(['farm' => 'yard']);
        $request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn(['gary' => 'barlow']);
        $request->expects($this->once())
            ->method('getHeader')
            ->with('authorization')
            ->willReturn([]);

        $handler = $this->createMock(RequestHandlerInterface::class);

        $html = Handler::html('jwt', 'Secret123!456$', '');

        $process = new JwtAuthMiddleware($html);

        $result = $process->process($request, $handler);

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertSame(401, $result->getStatusCode());
        $this->assertSame('Unauthorized: JSON Web Token not set in request.', $result->getReasonPhrase());
    }

    /**
     * @covers PsrJwt\JwtAuthMiddleware::__invoke
     * @uses PsrJwt\JwtAuthMiddleware::__construct
     * @uses PsrJwt\Auth\Authorise
     * @uses PsrJwt\Auth\Auth
     * @uses PsrJwt\Factory\Jwt
     * @uses PsrJwt\Validation\Validate
     * @uses PsrJwt\Parser\Parse
     * @uses PsrJwt\Parser\Bearer
     * @uses PsrJwt\Parser\Query
     * @uses PsrJwt\Parser\Cookie
     * @uses PsrJwt\Parser\Body
     * @uses PsrJwt\Handler\Html
     * @uses PsrJwt\Parser\Request
     * @uses PsrJwt\Handler\Html
     */
    public function testInvoke(): void
    {
        $jwt = new Jwt();
        $jwt = $jwt->builder('Secret123!456$');
        $token = $jwt->setIssuer('localhost')
            ->setExpiration(time() + 10)
            ->setPayloadClaim('nbf', time() - 60)
            ->build()
            ->getToken();

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getHeader')
            ->with('authorization')
            ->willReturn(['Bearer ' . $token]);

        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(200);
        $response->expects($this->once())
            ->method('getReasonPhrase')
            ->willReturn('Ok');

        $next = function ($request, $response) {
            return $response;
        };

        $html = Handler::html('jwt', 'Secret123!456$', '');

        $invokable = new JwtAuthMiddleware($html);

        $result = $invokable($request, $response, $next);

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertSame(200, $result->getStatusCode());
        $this->assertSame('Ok', $result->getReasonPhrase());
    }

    /**
     * @covers PsrJwt\JwtAuthMiddleware::__invoke
     * @uses PsrJwt\JwtAuthMiddleware
     * @uses PsrJwt\Auth\Authorise
     * @uses PsrJwt\Auth\Auth
     * @uses PsrJwt\Factory\Jwt
     * @uses PsrJwt\Validation\Validate
     * @uses PsrJwt\Parser\Parse
     * @uses PsrJwt\Parser\Body
     * @uses PsrJwt\Parser\Bearer
     * @uses PsrJwt\Parser\Query
     * @uses PsrJwt\Parser\Cookie
     * @uses PsrJwt\Handler\Html
     * @uses PsrJwt\Parser\Request
     */
    public function testInvokeFail(): void
    {
        $jwt = new Jwt();
        $jwt = $jwt->builder('Secret123!456$');
        $token = $jwt->setIssuer('localhost')
            ->setExpiration(time() + 10)
            ->setPayloadClaim('nbf', time() - 60)
            ->build()
            ->getToken();

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getCookieParams')
            ->willReturn(['car' => 'park']);
        $request->expects($this->once())
            ->method('getQueryParams')
            ->willReturn(['jwt' => substr($token, 0, -1)]);
        $request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn(['gary' => 'barlow']);
        $request->expects($this->once())
            ->method('getHeader')
            ->with('authorization')
            ->willReturn([]);

        $response = $this->createMock(ResponseInterface::class);

        $next = function ($request, $response) {
            return $response;
        };

        $html = Handler::html('jwt', 'secret', '');

        $invokable = new JwtAuthMiddleware($html);

        $result = $invokable($request, $response, $next);

        $this->assertSame(401, $result->getStatusCode());
        $this->assertSame('Unauthorized: Signature is invalid.', $result->getReasonPhrase());
    }
}
