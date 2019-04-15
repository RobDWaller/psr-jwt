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
    public function testJwtParse()
    {
        $parse = new JwtParse('jwt');
        $this->assertInstanceOf(JwtParse::class, $parse);
    }

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

    public function tearDown() {
        m::close();
    }
}
