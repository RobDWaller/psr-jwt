<?php

namespace Test\Parser;

use PHPUnit\Framework\TestCase;
use PsrJwt\Parser\Cookie;
use PsrJwt\Parser\ParserInterface;
use Psr\Http\Message\ServerRequestInterface;
use Mockery as m;

class CookieTest extends TestCase
{
    public function testCookie()
    {
        $cookie = new Cookie();

        $this->assertInstanceOf(Cookie::class, $cookie);
        $this->assertInstanceOf(ParserInterface::class, $cookie);
    }

    public function testParse()
    {
        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getCookieParams')
            ->once()
            ->andReturn(['jwt' => 'abc.def.ghi']);

        $cookie = new Cookie();
        $result = $cookie->parse($request);

        $this->assertSame(['jwt' => 'abc.def.ghi'], $result);
    }

    public function tearDown()
    {
        m::close();
    }
}
