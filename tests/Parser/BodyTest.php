<?php

namespace Test\Parser;

use PHPUnit\Framework\TestCase;
use PsrJwt\Parser\Body;
use PsrJwt\Parser\ParserInterface;
use Psr\Http\Message\ServerRequestInterface;
use Mockery as m;

class BodyTest extends TestCase
{
    public function testBody()
    {
        $body = new Body(['token_key' => 'jwt']);

        $this->assertInstanceOf(Body::class, $body);
        $this->assertInstanceOf(ParserInterface::class, $body);
    }

    public function testParse()
    {
        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getParsedBody')
            ->once()
            ->andReturn(['jwt' => 'abc.def.ghi']);

        $body = new Body(['token_key' => 'jwt']);
        $result = $body->parse($request);

        $this->assertSame('abc.def.ghi', $result);
    }

    public function tearDown()
    {
        m::close();
    }
}
