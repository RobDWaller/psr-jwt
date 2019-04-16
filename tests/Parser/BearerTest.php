<?php

namespace Test\Parser;

use PHPUnit\Framework\TestCase;
use PsrJwt\Parser\Bearer;
use PsrJwt\Parser\ParserInterface;
use Psr\Http\Message\ServerRequestInterface;
use Mockery as m;

class BearerTest extends TestCase
{
    public function testBearer()
    {
        $bearer = new Bearer();

        $this->assertInstanceOf(Bearer::class, $bearer);
        $this->assertInstanceOf(ParserInterface::class, $bearer);
    }

    public function testParse()
    {
        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getHeader')
            ->with('authorization')
            ->once()
            ->andReturn(['bearer abc.def.ghi']);

        $bearer = new Bearer();
        $result = $bearer->parse($request);

        $this->assertSame('abc.def.ghi', $result);
    }

    public function tearDown()
    {
        m::close();
    }
}
