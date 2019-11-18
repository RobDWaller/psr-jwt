<?php

namespace Tests\Parser;

use PHPUnit\Framework\TestCase;
use PsrJwt\Parser\Parse;
use PsrJwt\Parser\Request;
use Psr\Http\Message\ServerRequestInterface;
use Mockery as m;

class RequestTest extends TestCase
{
    public function testRequest()
    {
        $parse = new Parse(['token_key' => 'jwt']);

        $request = new Request($parse);

        $this->assertInstanceOf(Request::class, $request);
    }

    public function testHasToken()
    {
        $parse = m::mock(Parse::class);
        $parse->shouldReceive('addParser')
            ->times(4)
            ->shouldReceive('findToken')
            ->once()
            ->andReturn('abcdef.123.abcdef');

        $httpRequest = m::mock(ServerRequestInterface::class);

        $request = new Request($parse);

        $this->assertTrue($request->hasToken($httpRequest, 'jwt'));
    }

    public function testHasNoToken()
    {
        $parse = m::mock(Parse::class);
        $parse->shouldReceive('addParser')
            ->times(4)
            ->shouldReceive('findToken')
            ->once()
            ->andReturn('');

        $httpRequest = m::mock(ServerRequestInterface::class);

        $request = new Request($parse);

        $this->assertFalse($request->hasToken($httpRequest, 'jwt'));
    }

    public function testParse()
    {
        $parse = m::mock(Parse::class);
        $parse->shouldReceive('addParser')
            ->times(4)
            ->shouldReceive('findToken')
            ->once()
            ->andReturn('abcdef.123.abcdef');

        $httpRequest = m::mock(ServerRequestInterface::class);

        $request = new Request($parse);

        $this->assertSame('abcdef.123.abcdef', $request->parse($httpRequest, 'jwt'));
    }

    public function testParseNoToken()
    {
        $parse = m::mock(Parse::class);
        $parse->shouldReceive('addParser')
            ->times(4)
            ->shouldReceive('findToken')
            ->once()
            ->andReturn('');

        $httpRequest = m::mock(ServerRequestInterface::class);

        $request = new Request($parse);

        $this->assertSame('', $request->parse($httpRequest, 'jwt'));
    }

    public function tearDown(): void
    {
        m::close();
    }
}