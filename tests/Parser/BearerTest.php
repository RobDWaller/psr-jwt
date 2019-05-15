<?php

namespace Tests\Parser;

use PHPUnit\Framework\TestCase;
use PsrJwt\Parser\Bearer;
use PsrJwt\Parser\ParserInterface;
use Psr\Http\Message\ServerRequestInterface;
use Mockery as m;

class BearerTest extends TestCase
{
    /**
     * @covers PsrJwt\Parser\Bearer
     */
    public function testBearer()
    {
        $bearer = new Bearer();

        $this->assertInstanceOf(Bearer::class, $bearer);
        $this->assertInstanceOf(ParserInterface::class, $bearer);
    }

    /**
     * @covers PsrJwt\Parser\Bearer::parse
     */
    public function testParse()
    {
        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getHeader')
            ->with('authorization')
            ->once()
            ->andReturn(['Bearer abc.def.ghi']);

        $bearer = new Bearer();
        $result = $bearer->parse($request);

        $this->assertSame('abc.def.ghi', $result);
    }

    /**
     * @covers PsrJwt\Parser\Bearer::parse
     */
    public function testParseInvalid()
    {
        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getHeader')
            ->with('authorization')
            ->once()
            ->andReturn(['Bear']);

        $bearer = new Bearer();
        $result = $bearer->parse($request);

        $this->assertEmpty($result);
    }

    public function tearDown()
    {
        m::close();
    }
}
