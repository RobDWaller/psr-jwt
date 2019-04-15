<?php

namespace Test\Parser;

use PHPUnit\Framework\TestCase;
use PsrJwt\Parser\Cookie;
use PsrJwt\Parser\ParserInterface;

class CookieTest extends TestCase
{
    public function testCookie()
    {
        $cookie = new Cookie();

        $this->assertInstanceOf(Cookie::class, $cookie);
        $this->assertInstanceOf(ParserInterface::class, $cookie);
    }
}
