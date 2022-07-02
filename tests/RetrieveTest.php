<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use PsrJwt\Retrieve;
use PsrJwt\Location\LocationException;
use Psr\Http\Message\ServerRequestInterface;
use PsrJwt\Location\Bearer;
use PsrJwt\Location\Body;
use PsrJwt\Location\Query;

class RetrieveTest extends TestCase
{
    /**
     * @covers PsrJwt\Parser\Parse
     */
    public function testRetrieve(): void
    {
        $retrieve = new Retrieve([new Bearer()]);
        $this->assertInstanceOf(Retrieve::class, $retrieve);
    }

    /**
     * @covers PsrJwt\Parser\Parse::findToken
     * @covers PsrJwt\Parser\Parse::addParser
     * @uses PsrJwt\Parser\Parse
     * @uses PsrJwt\Parser\Bearer
     */
    public function testFindToken(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getHeader')
            ->with('authorization')
            ->willReturn(['Bearer abc.def.ghi']);

        $retrieve = new Retrieve([new Bearer()]);

        $result = $retrieve->findToken($request);

        $this->assertSame('abc.def.ghi', $result);
    }

    /**
     * @covers PsrJwt\Parser\Parse::findToken
     * @covers PsrJwt\Parser\Parse::addParser
     * @uses PsrJwt\Parser\Parse
     * @uses PsrJwt\Parser\Bearer
     * @uses PsrJwt\Parser\Body
     * @uses PsrJwt\Parser\Query
     */
    public function testFindTokenMultiParser(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getHeader')
            ->with('authorization')
            ->willReturn([]);
        $request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn(['jwt' => 'abc.123.ghi']);

        $retrieve = new Retrieve([
            new Bearer(),
            new Body('jwt'),
            new Query('jwt')
        ]);

        $result = $retrieve->findToken($request);

        $this->assertSame('abc.123.ghi', $result);
    }

    /**
     * @covers PsrJwt\Parser\Parse::findToken
     * @uses PsrJwt\Parser\Parse
     * @uses PsrJwt\Parser\ParseException
     */
    public function testFindTokenFail(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getHeader')
            ->with('authorization')
            ->willReturn([]);

        $retrieve = new Retrieve([new Bearer()]);
        $this->expectException(LocationException::class);
        $this->expectExceptionMessage("JSON Web Token not set in request.");
        $retrieve->findToken($request);
    }
}
