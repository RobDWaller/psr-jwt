<?php

declare(strict_types=1);

namespace Tests\Location;

use PHPUnit\Framework\TestCase;
use PsrJwt\Location\Bearer;
use PsrJwt\Location\LocationInterface;
use Psr\Http\Message\ServerRequestInterface;

class BearerTest extends TestCase
{
    /**
     * @covers PsrJwt\Location\Bearer
     */
    public function testBearer(): void
    {
        $bearer = new Bearer();

        $this->assertInstanceOf(Bearer::class, $bearer);
        $this->assertInstanceOf(LocationInterface::class, $bearer);
    }

    /**
     * @covers PsrJwt\Location\Bearer::find
     */
    public function testFind(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getHeader')
            ->with('authorization')
            ->willReturn(['Bearer abc.def.ghi']);

        $bearer = new Bearer();
        $result = $bearer->find($request);

        $this->assertSame('abc.def.ghi', $result);
    }

    /**
     * @covers PsrJwt\Location\Bearer::find
     */
    public function testFindInvalid(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getHeader')
            ->with('authorization')
            ->willReturn(['Bear']);

        $bearer = new Bearer();
        $result = $bearer->find($request);

        $this->assertEmpty($result);
    }
}
