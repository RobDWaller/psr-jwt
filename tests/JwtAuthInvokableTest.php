<?php

namespace Test;

use PHPUnit\Framework\TestCase;
use PsrJwt\JwtAuthInvokable;
use PsrJwt\JwtAuth;
use PsrJwt\JwtAuthException;
use ReflectionMethod;

class JwtAuthInvokableTest extends TestCase
{
    public function testJwtAuthInokable()
    {
        $invokable = new JwtAuthInvokable();

        $this->assertInstanceOf(JwtAuthInvokable::class, $invokable);
    }

    public function testJwtAuthInokableIsJwtAuth()
    {
        $invokable = new JwtAuthInvokable();

        $this->assertInstanceOf(JwtAuth::class, $invokable);
    }

    public function testJwtAuthHasJwt()
    {
        $invokable = new JwtAuthInvokable();

        $data['jwt'] = 'abc.abc.abc';
        $data['foo'] = 'bar';

        $method = new ReflectionMethod(JwtAuth::class, 'hasJwt');
        $method->setAccessible(true);
        $result = $method->invokeArgs($invokable, [$data]);

        $this->assertTrue($result);
    }

    public function testJwtAuthHasJwtFalse()
    {
        $invokable = new JwtAuthInvokable();

        $data['token'] = 'abc.abc.abc';
        $data['foo'] = 'bar';

        $method = new ReflectionMethod(JwtAuth::class, 'hasJwt');
        $method->setAccessible(true);
        $result = $method->invokeArgs($invokable, [$data]);

        $this->assertFalse($result);
    }

    public function testGetToken()
    {
        $server = ['jwt' => 'abc.def.ghi'];
        $cookie = ['foo' => 'bar'];
        $query = ['hello' => 'world'];
        $body = ['car' => 'park'];

        $invokable = new JwtAuthInvokable();

        $method = new ReflectionMethod(JwtAuth::class, 'getToken');
        $method->setAccessible(true);
        $result = $method->invokeArgs($invokable, [$server, $cookie, $query, $body]);

        $this->assertSame($result, 'abc.def.ghi');
    }

    public function testGetTokenFromCookie()
    {
        $server = ['foo' => 'bar'];
        $cookie = ['jwt' => 'abc.def.ghi'];
        $query = ['hello' => 'world'];
        $body = ['car' => 'park'];

        $invokable = new JwtAuthInvokable();

        $method = new ReflectionMethod(JwtAuth::class, 'getToken');
        $method->setAccessible(true);
        $result = $method->invokeArgs($invokable, [$server, $cookie, $query, $body]);

        $this->assertSame($result, 'abc.def.ghi');
    }

    public function testGetTokenFromQuery()
    {
        $server = ['foo' => 'bar'];
        $cookie = ['hello' => 'world'];
        $query = ['jwt' => 'abc.def.ghi'];
        $body = ['car' => 'park'];

        $invokable = new JwtAuthInvokable();

        $method = new ReflectionMethod(JwtAuth::class, 'getToken');
        $method->setAccessible(true);
        $result = $method->invokeArgs($invokable, [$server, $cookie, $query, $body]);

        $this->assertSame($result, 'abc.def.ghi');
    }

    public function testGetTokenFromBody()
    {
        $server = ['foo' => 'bar'];
        $cookie = ['hello' => 'world'];
        $query = ['car' => 'park'];
        $body = ['jwt' => 'abc.def.ghi'];

        $invokable = new JwtAuthInvokable();

        $method = new ReflectionMethod(JwtAuth::class, 'getToken');
        $method->setAccessible(true);
        $result = $method->invokeArgs($invokable, [$server, $cookie, $query, $body]);

        $this->assertSame($result, 'abc.def.ghi');
    }

    /**
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

        $invokable = new JwtAuthInvokable();

        $method = new ReflectionMethod(JwtAuth::class, 'getToken');
        $method->setAccessible(true);
        $method->invokeArgs($invokable, [$server, $cookie, $query, $body]);
    }
}
