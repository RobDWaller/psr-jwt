<?php

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
use Mockery as m;
use ReflectionMethod;

class JwtAuthMiddlewareTest extends TestCase
{
    /**
     * @covers PsrJwt\JwtAuthMiddleware
     * @uses PsrJwt\Auth\Authorise
     * @uses PsrJwt\Handler\Html
     */
    public function testJwtAuthProcess(): void
    {
        $authorise = new Html('secret', 'jwt', '');

        $process = new JwtAuthMiddleware($authorise);

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

        $authorise = new Html('Secret123!456$', '', '');

        $process = new JwtAuthMiddleware($authorise);

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
        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getCookieParams')
            ->once()
            ->andReturn(['car' => 'park']);
        $request->shouldReceive('getQueryParams')
            ->once()
            ->andReturn(['farm' => 'yard']);
        $request->shouldReceive('getParsedBody')
            ->twice()
            ->andReturn(['gary' => 'barlow']);
        $request->shouldReceive('getHeader')
            ->with('authorization')
            ->once()
            ->andReturn([]);

        $handler = m::mock(RequestHandlerInterface::class);

        $authorise = new Html('Secret123!456$', 'jwt', '');

        $process = new JwtAuthMiddleware($authorise);

        $result = $process->process($request, $handler);

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertSame(400, $result->getStatusCode());
        $this->assertSame('Bad Request: JSON Web Token not set in request.', $result->getReasonPhrase());
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

        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getHeader')
            ->with('authorization')
            ->once()
            ->andReturn(['Bearer ' . $token]);

        $response = m::mock(ResponseInterface::class);
        $response->shouldReceive('getStatusCode')
            ->once()
            ->andReturn(200);
        $response->shouldReceive('getReasonPhrase')
            ->once()
            ->andReturn('Ok');

        $next = function ($request, $response) {
            return $response;
        };

        $auth = new Html('Secret123!456$', 'jwt', '');

        $invokable = new JwtAuthMiddleware($auth);

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

        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getCookieParams')
            ->once()
            ->andReturn(['car' => 'park']);
        $request->shouldReceive('getQueryParams')
            ->once()
            ->andReturn(['jwt' => substr($token, 0, -1)]);
        $request->shouldReceive('getParsedBody')
            ->twice()
            ->andReturn(['gary' => 'barlow']);
        $request->shouldReceive('getHeader')
            ->with('authorization')
            ->once()
            ->andReturn([]);

        $response = m::mock(ResponseInterface::class);

        $next = function ($request, $response) {
            return $response;
        };

        $auth = new Html('secret', 'jwt', '');

        $invokable = new JwtAuthMiddleware($auth);

        $result = $invokable($request, $response, $next);

        $this->assertSame(401, $result->getStatusCode());
        $this->assertSame('Unauthorized: Signature is invalid.', $result->getReasonPhrase());
    }

    public function tearDown(): void
    {
        m::close();
    }
}
