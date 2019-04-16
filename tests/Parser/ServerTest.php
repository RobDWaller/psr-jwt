<?php

namespace Test\Parser;

use PHPUnit\Framework\TestCase;
use PsrJwt\Parser\Server;
use PsrJwt\Parser\ParserInterface;
use Psr\Http\Message\ServerRequestInterface;
use Mockery as m;

class ServerTest extends TestCase
{
    public function testserver()
    {
        $server = new Server(['token_key' => 'jwt']);

        $this->assertInstanceOf(Server::class, $server);
        $this->assertInstanceOf(ParserInterface::class, $server);
    }

    public function testParse()
    {
        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getserverParams')
            ->once()
            ->andReturn(['jwt' => 'abc.def.ghi']);

        $server = new Server(['token_key' => 'jwt']);
        $result = $server->parse($request);

        $this->assertSame('abc.def.ghi', $result);
    }

    public function tearDown()
    {
        m::close();
    }
}
