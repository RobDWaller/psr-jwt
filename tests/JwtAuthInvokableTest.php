<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use PsrJwt\JwtAuthInvokable;
use PsrJwt\Jwt;
use PsrJwt\JwtAuth;
use PsrJwt\JwtAuthException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionMethod;
use Mockery as m;

class JwtAuthInvokableTest extends TestCase
{
    /**
     * @covers PsrJwt\JwtAuthInvokable
     */
    public function testJwtAuthInokable()
    {
        $invokable = new JwtAuthInvokable('secret');

        $this->assertInstanceOf(JwtAuthInvokable::class, $invokable);
    }

    /**
     * @covers PsrJwt\JwtAuthInvokable
     */
    public function testJwtAuthInokableIsJwtAuth()
    {
        $invokable = new JwtAuthInvokable('secret');

        $this->assertInstanceOf(JwtAuthInvokable::class, $invokable);
    }

    /**
     * @covers PsrJwt\JwtAuth::hasJwt
     */
    public function testJwtAuthHasJwt()
    {
        $invokable = new JwtAuthInvokable('secret');

        $data['jwt'] = 'abc.abc.abc';
        $data['foo'] = 'bar';

        $method = new ReflectionMethod(JwtAuthInvokable::class, 'hasJwt');
        $method->setAccessible(true);
        $result = $method->invokeArgs($invokable, [$data]);

        $this->assertTrue($result);
    }

    /**
     * @covers PsrJwt\JwtAuth::hasJwt
     */
    public function testJwtAuthHasJwtFalse()
    {
        $invokable = new JwtAuthInvokable('secret');

        $data['token'] = 'abc.abc.abc';
        $data['foo'] = 'bar';

        $method = new ReflectionMethod(JwtAuthInvokable::class, 'hasJwt');
        $method->setAccessible(true);
        $result = $method->invokeArgs($invokable, [$data]);

        $this->assertFalse($result);
    }

    /**
     * @covers PsrJwt\JwtAuth::hasJwt
     */
    public function testJwtAuthHasJwtEmpty()
    {
        $invokable = new JwtAuthInvokable('secret');

        $data = [];

        $method = new ReflectionMethod(JwtAuthInvokable::class, 'hasJwt');
        $method->setAccessible(true);
        $result = $method->invokeArgs($invokable, [$data]);

        $this->assertFalse($result);
    }

    /**
     * @covers PsrJwt\JwtAuth::getToken
     * @uses PsrJwt\JwtAuth::hasJwt
     */
    public function testGetToken()
    {
        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getServerParams')
            ->twice()
            ->andReturn(['jwt' => 'abc.def.ghi']);

        $invokable = new JwtAuthInvokable('secret');

        $method = new ReflectionMethod(JwtAuthInvokable::class, 'getToken');
        $method->setAccessible(true);
        $result = $method->invokeArgs($invokable, [$request]);

        $this->assertSame($result, 'abc.def.ghi');
    }

    /**
     * @covers PsrJwt\JwtAuth::getToken
     * @uses PsrJwt\JwtAuth::hasJwt
     */
    public function testGetTokenFromCookie()
    {
        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getServerParams')
            ->once()
            ->andReturn(['foo' => 'bar']);
        $request->shouldReceive('getCookieParams')
            ->twice()
            ->andReturn(['jwt' => 'abc.def.ghi']);

        $invokable = new JwtAuthInvokable('secret');

        $method = new ReflectionMethod(JwtAuthInvokable::class, 'getToken');
        $method->setAccessible(true);
        $result = $method->invokeArgs($invokable, [$request]);

        $this->assertSame($result, 'abc.def.ghi');
    }

    /**
     * @covers PsrJwt\JwtAuth::getToken
     * @uses PsrJwt\JwtAuth::hasJwt
     */
    public function testGetTokenFromQuery()
    {
        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getServerParams')
            ->once()
            ->andReturn(['foo' => 'bar']);
        $request->shouldReceive('getCookieParams')
            ->once()
            ->andReturn(['hello' => 'world']);
        $request->shouldReceive('getQueryParams')
            ->twice()
            ->andReturn(['jwt' => 'abc.def.ghi']);

        $invokable = new JwtAuthInvokable('secret');

        $method = new ReflectionMethod(JwtAuthInvokable::class, 'getToken');
        $method->setAccessible(true);
        $result = $method->invokeArgs($invokable, [$request]);

        $this->assertSame($result, 'abc.def.ghi');
    }

    /**
     * @covers PsrJwt\JwtAuth::getToken
     * @uses PsrJwt\JwtAuth::hasJwt
     */
    public function testGetTokenFromBody()
    {
        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getServerParams')
            ->once()
            ->andReturn(['foo' => 'bar']);
        $request->shouldReceive('getCookieParams')
            ->once()
            ->andReturn(['hello' => 'world']);
        $request->shouldReceive('getQueryParams')
            ->once()
            ->andReturn(['car' => 'park']);
        $request->shouldReceive('getParsedBody')
            ->twice()
            ->andReturn(['jwt' => 'abc.def.ghi']);

        $invokable = new JwtAuthInvokable('secret');

        $method = new ReflectionMethod(JwtAuthInvokable::class, 'getToken');
        $method->setAccessible(true);
        $result = $method->invokeArgs($invokable, [$request]);

        $this->assertSame($result, 'abc.def.ghi');
    }

    /**
     * @covers PsrJwt\JwtAuth::getToken
     * @uses PsrJwt\JwtAuth::hasJwt
     * @uses PsrJwt\JwtAuthException::__construct
     * @expectedException PsrJwt\JwtAuthException
     * @expectedMessage JWT Token not set
     * @expectedExceptionCode 1
     */
    public function testGetTokenNoJwt()
    {
        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getServerParams')
            ->once()
            ->andReturn(['foo' => 'bar']);
        $request->shouldReceive('getCookieParams')
            ->once()
            ->andReturn(['hello' => 'world']);
        $request->shouldReceive('getQueryParams')
            ->once()
            ->andReturn(['car' => 'park']);
        $request->shouldReceive('getParsedBody')
            ->once()
            ->andReturn(['gary' => 'barlow']);

        $invokable = new JwtAuthInvokable('secret');

        $method = new ReflectionMethod(JwtAuthInvokable::class, 'getToken');
        $method->setAccessible(true);
        $method->invokeArgs($invokable, [$request]);
    }

    /**
     * @covers PsrJwt\JwtAuth::getSecret
     * @uses PsrJwt\JwtAuthInvokable::__construct
     */
    public function testGetSecret()
    {
        $invokable = new JwtAuthInvokable('secret');

        $method = new ReflectionMethod(JwtAuthInvokable::class, 'getSecret');
        $method->setAccessible(true);
        $result = $method->invoke($invokable);

        $this->assertSame('secret', $result);
    }

    /**
     * @covers PsrJwt\JwtAuth::validate
     */
    public function testValidate()
    {
        $jwt = Jwt::builder();
        $token = $jwt->setSecret('Secret123!456$')
            ->setIssuer('localhost')
            ->build()
            ->getToken();

        $invokable = new JwtAuthInvokable('Secret123!456$');

        $method = new ReflectionMethod(JwtAuthInvokable::class, 'validate');
        $method->setAccessible(true);
        $result = $method->invokeArgs($invokable, [$token]);

        $this->assertTrue($result);
    }

    /**
     * @covers PsrJwt\JwtAuth::validate
     * @expectedException PsrJwt\JwtAuthException
     * @expectedExceptionMessage Signature is invalid.
     * @expectedExceptionCode 3
     */
    public function testValidateBadSecret()
    {
        $jwt = Jwt::builder();
        $token = $jwt->setSecret('Secret123!456$')
            ->setIssuer('localhost')
            ->build()
            ->getToken();

        $invokable = new JwtAuthInvokable('Secret');

        $method = new ReflectionMethod(JwtAuthInvokable::class, 'validate');
        $method->setAccessible(true);
        $method->invokeArgs($invokable, [$token]);
    }

    /**
     * @covers PsrJwt\JwtAuth::validate
     * @expectedException PsrJwt\JwtAuthException
     * @expectedExceptionMessage Expiration claim has expired.
     * @expectedExceptionCode 4
     */
    public function testValidateBadExpiration()
    {
        $jwt = Jwt::builder();
        $token = $jwt->setSecret('Secret123!456$')
            ->setIssuer('localhost')
            ->setPayloadClaim('exp', time() - 10)
            ->build()
            ->getToken();

        $invokable = new JwtAuthInvokable('Secret123!456$');

        $method = new ReflectionMethod(JwtAuthInvokable::class, 'validate');
        $method->setAccessible(true);
        $method->invokeArgs($invokable, [$token]);
    }

    /**
     * @covers PsrJwt\JwtAuth::validate
     * @expectedException PsrJwt\JwtAuthException
     * @expectedExceptionMessage Not Before claim has not elapsed.
     * @expectedExceptionCode 5
     */
    public function testValidateBadNotBefore()
    {
        $jwt = Jwt::builder();
        $token = $jwt->setSecret('Secret123!456$')
            ->setIssuer('localhost')
            ->setPayloadClaim('nbf', time() + 60)
            ->build()
            ->getToken();

        $invokable = new JwtAuthInvokable('Secret123!456$');

        $method = new ReflectionMethod(JwtAuthInvokable::class, 'validate');
        $method->setAccessible(true);
        $method->invokeArgs($invokable, [$token]);
    }

    /**
     * @covers PsrJwt\JwtAuthInvokable::__invoke
     */
    public function testInvoke()
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

        $next = function($request, $response) {
            return $response;
        };

        $invokable = new JwtAuthInvokable('Secret123!456$');

        $result = $invokable($request, $response, $next);

        $this->assertInstanceOf(ResponseInterface::class, $result);
    }

    /**
     * @expectedException PsrJwt\JwtAuthException
     */
    public function testInvokeFail()
    {
        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getServerParams')
            ->twice()
            ->andReturn(['jwt' => 'abc.abc.abc']);

        $response = m::mock(ResponseInterface::class);

        $next = function($request, $response) {
            return $response;
        };

        $invokable = new JwtAuthInvokable('secret');

        $result = $invokable($request, $response, $next);
    }
}
