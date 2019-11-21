<?php

namespace Tests\Parser;

use PHPUnit\Framework\TestCase;
use PsrJwt\Parser\Parse;
use PsrJwt\Parser\Request;
use Psr\Http\Message\ServerRequestInterface;
use Mockery as m;

class RequestTest extends TestCase
{
    /**
     * @covers PsrJwt\Parser\Request
     * @uses PsrJwt\Parser\Parse
     */
    public function testRequest()
    {
        $parse = new Parse();

        $request = new Request($parse);

        $this->assertInstanceOf(Request::class, $request);
    }

    /**
     * @covers PsrJwt\Parser\Request::parse
     * @uses PsrJwt\Parser\Request
     * @uses PsrJwt\Parser\Bearer
     * @uses PsrJwt\Parser\Cookie
     * @uses PsrJwt\Parser\Body
     * @uses PsrJwt\Parser\Query
     */
    public function testParse()
    {
        $parse = m::mock(Parse::class);
        $parse->shouldReceive('addParser')
            ->times(4);
            
        $parse->shouldReceive('findToken')
            ->once()
            ->andReturn('abcdef.123.abcdef');

        $httpRequest = m::mock(ServerRequestInterface::class);

        $request = new Request($parse);

        $this->assertSame('abcdef.123.abcdef', $request->parse($httpRequest, 'jwt'));
    }

    /**
     * @covers PsrJwt\Parser\Request::parse
     * @uses PsrJwt\Parser\Request
     * @uses PsrJwt\Parser\Bearer
     * @uses PsrJwt\Parser\Cookie
     * @uses PsrJwt\Parser\Body
     * @uses PsrJwt\Parser\Query
     * @uses PsrJwt\Parser\ParseException
     */
    public function testParseNoToken()
    {
        $parse = m::mock(Parse::class);
        $parse->shouldReceive('addParser')
            ->times(4);
            
        $parse->shouldReceive('findToken')
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
