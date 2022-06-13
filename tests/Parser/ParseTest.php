<?php

declare(strict_types=1);

namespace Tests\Parser;

use PHPUnit\Framework\TestCase;
use PsrJwt\Parser\Parse;
use PsrJwt\Parser\ParseException;
use Psr\Http\Message\ServerRequestInterface;
use PsrJwt\Parser\Bearer;
use PsrJwt\Parser\Body;
use PsrJwt\Parser\Query;

class ParseTest extends TestCase
{
    /**
     * @covers PsrJwt\Parser\Parse
     */
    public function testParse(): void
    {
        $parse = new Parse();
        $this->assertInstanceOf(Parse::class, $parse);
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

        $parse = new Parse();
        $parse->addParser(new Bearer());

        $result = $parse->findToken($request);

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

        $parse = new Parse();
        $parse->addParser(new Bearer());
        $parse->addParser(new Body('jwt'));
        $parse->addParser(new Query('jwt'));

        $result = $parse->findToken($request);

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

        $parse = new Parse();
        $this->expectException(ParseException::class);
        $this->expectExceptionMessage("JSON Web Token not set in request.");
        $parse->findToken($request);
    }
}
