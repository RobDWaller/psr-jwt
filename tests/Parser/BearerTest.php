<?php

declare(strict_types=1);

namespace Tests\Parser;

use PHPUnit\Framework\TestCase;
use PsrJwt\Parser\Bearer;
use PsrJwt\Parser\ParserInterface;
use Psr\Http\Message\ServerRequestInterface;

class BearerTest extends TestCase
{
    /**
     * @covers PsrJwt\Parser\Bearer
     */
    public function testBearer(): void
    {
        $bearer = new Bearer();

        $this->assertInstanceOf(Bearer::class, $bearer);
        $this->assertInstanceOf(ParserInterface::class, $bearer);
    }

    /**
     * @covers PsrJwt\Parser\Bearer::parse
     */
    public function testParse(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getHeader')
            ->with('authorization')
            ->willReturn(['Bearer abc.def.ghi']);

        $bearer = new Bearer();
        $result = $bearer->parse($request);

        $this->assertSame('abc.def.ghi', $result);
    }

    /**
     * @covers PsrJwt\Parser\Bearer::parse
     */
    public function testParseInvalid(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getHeader')
            ->with('authorization')
            ->willReturn(['Bear']);

        $bearer = new Bearer();
        $result = $bearer->parse($request);

        $this->assertEmpty($result);
    }
}
