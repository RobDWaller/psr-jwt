<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use PsrJwt\JwtAuthInvokable;
use PsrJwt\JwtAuth;
use PsrJwt\JwtAuthException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReallySimpleJWT\Token;
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
     * @covers PsrJwt\JwtAuth::getToken
     * @uses PsrJwt\JwtAuth::hasJwt
     */
    public function testGetToken()
    {
        $server = ['jwt' => 'abc.def.ghi'];
        $cookie = ['foo' => 'bar'];
        $query = ['hello' => 'world'];
        $body = ['car' => 'park'];

        $invokable = new JwtAuthInvokable('secret');

        $method = new ReflectionMethod(JwtAuthInvokable::class, 'getToken');
        $method->setAccessible(true);
        $result = $method->invokeArgs($invokable, [$server, $cookie, $query, $body]);

        $this->assertSame($result, 'abc.def.ghi');
    }

    /**
     * @covers PsrJwt\JwtAuth::getToken
     * @uses PsrJwt\JwtAuth::hasJwt
     */
    public function testGetTokenFromCookie()
    {
        $server = ['foo' => 'bar'];
        $cookie = ['jwt' => 'abc.def.ghi'];
        $query = ['hello' => 'world'];
        $body = ['car' => 'park'];

        $invokable = new JwtAuthInvokable('secret');

        $method = new ReflectionMethod(JwtAuthInvokable::class, 'getToken');
        $method->setAccessible(true);
        $result = $method->invokeArgs($invokable, [$server, $cookie, $query, $body]);

        $this->assertSame($result, 'abc.def.ghi');
    }

    /**
     * @covers PsrJwt\JwtAuth::getToken
     * @uses PsrJwt\JwtAuth::hasJwt
     */
    public function testGetTokenFromQuery()
    {
        $server = ['foo' => 'bar'];
        $cookie = ['hello' => 'world'];
        $query = ['jwt' => 'abc.def.ghi'];
        $body = ['car' => 'park'];

        $invokable = new JwtAuthInvokable('secret');

        $method = new ReflectionMethod(JwtAuthInvokable::class, 'getToken');
        $method->setAccessible(true);
        $result = $method->invokeArgs($invokable, [$server, $cookie, $query, $body]);

        $this->assertSame($result, 'abc.def.ghi');
    }

    /**
     * @covers PsrJwt\JwtAuth::getToken
     * @uses PsrJwt\JwtAuth::hasJwt
     */
    public function testGetTokenFromBody()
    {
        $server = ['foo' => 'bar'];
        $cookie = ['hello' => 'world'];
        $query = ['car' => 'park'];
        $body = ['jwt' => 'abc.def.ghi'];

        $invokable = new JwtAuthInvokable('secret');

        $method = new ReflectionMethod(JwtAuthInvokable::class, 'getToken');
        $method->setAccessible(true);
        $result = $method->invokeArgs($invokable, [$server, $cookie, $query, $body]);

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
        $server = ['foo' => 'bar'];
        $cookie = ['hello' => 'world'];
        $query = ['car' => 'park'];
        $body = ['michael' => 'jackson'];

        $invokable = new JwtAuthInvokable('secret');

        $method = new ReflectionMethod(JwtAuthInvokable::class, 'getToken');
        $method->setAccessible(true);
        $method->invokeArgs($invokable, [$server, $cookie, $query, $body]);
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
     * @covers PsrJwt\JwtAuthInvokable::__invoke
     */
     public function testInvoke()
     {
         $request = m::mock(ServerRequestInterface::class);

         $response = m::mock(ResponseInterface::class);

         $next = function($request, $response) {
             return $response;
         };

         $invokable = new JwtAuthInvokable('secret');

         $result = $invokable($request, $response, $next);

         $this->assertInstanceOf(ResponseInterface::class, $result);
     }
}
