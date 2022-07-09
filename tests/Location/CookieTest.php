<?php

declare(strict_types=1);

namespace Tests\Location;

use PHPUnit\Framework\TestCase;
use PsrJwt\Location\Cookie;
use PsrJwt\Location\LocationInterface;
use Psr\Http\Message\ServerRequestInterface;

class CookieTest extends TestCase
{
    /**
     * @covers PsrJwt\Location\Cookie::__construct
     */
    public function testCookie(): void
    {
        $cookie = new Cookie('jwt');

        $this->assertInstanceOf(Cookie::class, $cookie);
        $this->assertInstanceOf(LocationInterface::class, $cookie);
    }

    /**
     * @covers PsrJwt\Location\Cookie::find
     */
    public function testFind(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getCookieParams')
            ->willReturn(['jwt' => 'abc.def.ghi']);

        $cookie = new Cookie('jwt');
        $result = $cookie->find($request);

        $this->assertSame('abc.def.ghi', $result);
    }
}
