<?php

declare(strict_types=1);

namespace Tests\Parser;

use PHPUnit\Framework\TestCase;
use PsrJwt\Parser\Parse;
use PsrJwt\Parser\Request;
use Psr\Http\Message\ServerRequestInterface;

class RequestTest extends TestCase
{
    /**
     * @covers PsrJwt\Parser\Request
     * @uses PsrJwt\Parser\Parse
     */
    public function testRequest(): void
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
    public function testParse(): void
    {
        $parse = $this->createMock(Parse::class);
        $parse->expects($this->exactly(4))
            ->method('addParser');

        $parse->expects($this->once())
            ->method('findToken')
            ->willReturn('abcdef.123.abcdef');

        $httpRequest = $this->createMock(ServerRequestInterface::class);

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
    public function testParseNoToken(): void
    {
        $parse = $this->createMock(Parse::class);
        $parse->expects($this->exactly(4))
            ->method('addParser');

        $parse->expects($this->once())
            ->method('findToken')
            ->willReturn('');

        $httpRequest = $this->createMock(ServerRequestInterface::class);

        $request = new Request($parse);

        $this->expectException(\PsrJwt\Parser\ParseException::class);
        $this->expectExceptionMessage('JSON Web Token not set in request.');
        $request->parse($httpRequest, 'jwt');
    }
}
