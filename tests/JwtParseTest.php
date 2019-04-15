<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use PsrJwt\JwtParse;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionMethod;
use Mockery as m;
use stdClass;

class JwtParseTest extends TestCase
{
    /**
     * @covers PsrJwt\JwtParse::__construct
     */
    public function testJwtParse()
    {
        $parse = new JwtParse('jwt');
        $this->assertInstanceOf(JwtParse::class, $parse);
    }

    /**
     * @covers PsrJwt\JwtParse::findToken
     * @uses PsrJwt\JwtParse
     */
    public function testFindToken()
    {
        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getHeader')
            ->with('authorization')
            ->once()
            ->andReturn(['bearer abc.def.ghi']);

        $parse = new JwtParse('jwt');
        $result = $parse->findToken($request);

        $this->assertSame(['jwt' => 'abc.def.ghi'], $result);
    }

    /**
     * @covers PsrJwt\JwtParse::findToken
     * @uses PsrJwt\JwtParse
     */
    public function testFindTokenCookie()
    {
        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getHeader')
            ->with('authorization')
            ->once()
            ->andReturn([]);
        $request->shouldReceive('getCookieParams')
            ->once()
            ->andReturn(['jwt' => 'abc.def.ghi']);

        $parse = new JwtParse('jwt');
        $result = $parse->findToken($request);

        $this->assertSame(['jwt' => 'abc.def.ghi'], $result);
    }

    /**
     * @covers PsrJwt\JwtParse::findToken
     * @uses PsrJwt\JwtParse
     */
    public function testFindTokenQuery()
    {
        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getHeader')
            ->with('authorization')
            ->once()
            ->andReturn([]);
        $request->shouldReceive('getCookieParams')
            ->once()
            ->andReturn([]);
        $request->shouldReceive('getQueryParams')
            ->once()
            ->andReturn(['jwt' => 'abc.def.ghi']);

        $parse = new JwtParse('jwt');
        $result = $parse->findToken($request);

        $this->assertSame(['jwt' => 'abc.def.ghi'], $result);
    }

    /**
     * @covers PsrJwt\JwtParse::findToken
     * @uses PsrJwt\JwtParse
     */
    public function testFindTokenBody()
    {
        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getHeader')
            ->with('authorization')
            ->once()
            ->andReturn([]);
        $request->shouldReceive('getCookieParams')
            ->once()
            ->andReturn([]);
        $request->shouldReceive('getQueryParams')
            ->once()
            ->andReturn([]);
        $request->shouldReceive('getParsedBody')
            ->once()
            ->andReturn(['jwt' => 'abc.def.ghi']);

        $parse = new JwtParse('jwt');
        $result = $parse->findToken($request);

        $this->assertSame(['jwt' => 'abc.def.ghi'], $result);
    }

    /**
     * @covers PsrJwt\JwtParse::findToken
     * @uses PsrJwt\JwtParse
     */
    public function testFindTokenServer()
    {
        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getHeader')
            ->with('authorization')
            ->once()
            ->andReturn([]);
        $request->shouldReceive('getCookieParams')
            ->once()
            ->andReturn([]);
        $request->shouldReceive('getQueryParams')
            ->once()
            ->andReturn([]);
        $request->shouldReceive('getParsedBody')
            ->twice()
            ->andReturn([]);
        $request->shouldReceive('getServerParams')
            ->once()
            ->andReturn(['jwt' => 'abc.def.ghi']);

        $parse = new JwtParse('jwt');
        $result = $parse->findToken($request);

        $this->assertSame(['jwt' => 'abc.def.ghi'], $result);
    }

    /**
     * @covers PsrJwt\JwtParse::getFromBody
     * @uses PsrJwt\JwtParse
     */
    public function testGetFromBody()
    {
        $parse = new JwtParse('jwt');

        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getParsedBody')
            ->once()
            ->andReturn(['jwt' => 'abc.abc.abc']);

        $method = new ReflectionMethod(JwtParse::class, 'getFromBody');
        $method->setAccessible(true);
        $result = $method->invokeArgs($parse, [$request]);

        $this->assertCount(1, $result);
        $this->assertSame('abc.abc.abc', $result['jwt']);
    }

    /**
     * @covers PsrJwt\JwtParse::getFromBody
     * @uses PsrJwt\JwtParse
     */
    public function testGetFromBodyNull()
    {
        $parse = new JwtParse('jwt');

        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getParsedBody')
            ->twice()
            ->andReturn(null);

        $method = new ReflectionMethod(JwtParse::class, 'getFromBody');
        $method->setAccessible(true);
        $result = $method->invokeArgs($parse, [$request]);

        $this->assertEmpty($result);
    }

    /**
     * @covers PsrJwt\JwtParse::getFromBody
     * @uses PsrJwt\JwtParse
     */
    public function testGetFromBodyObject()
    {
        $parse = new JwtParse('jwt');

        $object = new stdClass();
        $object->jwt = 'abc.def.ghi';

        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getParsedBody')
            ->twice()
            ->andReturn($object);

        $method = new ReflectionMethod(JwtParse::class, 'getFromBody');
        $method->setAccessible(true);
        $result = $method->invokeArgs($parse, [$request]);

        $this->assertCount(1, $result);
        $this->assertSame('abc.def.ghi', $result['jwt']);
    }

    /**
     * @covers PsrJwt\JwtParse::getFromBody
     * @uses PsrJwt\JwtParse
     */
    public function testGetFromBodyObjectNoKey()
    {
        $parse = new JwtParse('jwt');

        $object = new stdClass();

        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getParsedBody')
            ->twice()
            ->andReturn($object);

        $method = new ReflectionMethod(JwtParse::class, 'getFromBody');
        $method->setAccessible(true);
        $result = $method->invokeArgs($parse, [$request]);

        $this->assertEmpty($result);
    }

    /**
     * @covers PsrJwt\JwtParse::getFromBearer
     * @uses PsrJwt\JwtParse
     */
    public function testGetFromBearer()
    {
        $parse = new JwtParse('jwt');

        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getHeader')
            ->with('authorization')
            ->andReturn(['bearer abc.def.ghi']);

        $method = new ReflectionMethod(JwtParse::class, 'getFromBearer');
        $method->setAccessible(true);
        $result = $method->invokeArgs($parse, [$request]);

        $this->assertSame('abc.def.ghi', $result['jwt']);
    }

    /**
     * @covers PsrJwt\JwtParse::getFromBearer
     * @uses PsrJwt\JwtParse
     */
    public function testGetFromBearerTokenNoBearer()
    {
        $parse = new JwtParse('jwt');

        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getHeader')
            ->with('authorization')
            ->andReturn(['foo', 'bar']);

        $method = new ReflectionMethod(JwtParse::class, 'getFromBearer');
        $method->setAccessible(true);
        $result = $method->invokeArgs($parse, [$request]);

        $this->assertEmpty($result);
    }

    /**
     * @covers PsrJwt\JwtParse::getFromBearer
     * @uses PsrJwt\JwtParse
     */
    public function testGetFromBearerNoAuthorization()
    {
        $parse = new JwtParse('jwt');

        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getHeader')
            ->with('authorization')
            ->andReturn([]);

        $method = new ReflectionMethod(JwtParse::class, 'getFromBearer');
        $method->setAccessible(true);
        $result = $method->invokeArgs($parse, [$request]);

        $this->assertEmpty($result);
    }

    public function tearDown() {
        m::close();
    }
}
