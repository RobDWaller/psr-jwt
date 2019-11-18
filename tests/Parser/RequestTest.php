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

        $this->expectException(\PsrJwt\Parser\ParseException::class);
        $this->expectExceptionMessage('JSON Web Token not set in request.');
        $request->parse($httpRequest, 'jwt');
    }

    public function tearDown(): void
    {
        m::close();
    }
}