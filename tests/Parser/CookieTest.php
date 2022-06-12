<?php

declare(strict_types=1);

namespace Tests\Parser;

use PHPUnit\Framework\TestCase;
use PsrJwt\Parser\Cookie;
use PsrJwt\Parser\ParserInterface;
use Psr\Http\Message\ServerRequestInterface;

class CookieTest extends TestCase
{
    /**
     * @covers PsrJwt\Parser\Cookie::__construct
     */
    public function testCookie(): void
    {
        $cookie = new Cookie('jwt');

        $this->assertInstanceOf(Cookie::class, $cookie);
        $this->assertInstanceOf(ParserInterface::class, $cookie);
    }

    /**
     * @covers PsrJwt\Parser\Cookie::parse
     * @uses PsrJwt\Parser\Cookie::__construct
     */
    public function testParse(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getCookieParams')
            ->willReturn(['jwt' => 'abc.def.ghi']);

        $cookie = new Cookie('jwt');
        $result = $cookie->parse($request);

        $this->assertSame('abc.def.ghi', $result);
    }
}
