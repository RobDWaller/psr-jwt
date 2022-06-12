<?php

declare(strict_types=1);

namespace Tests\Parser;

use PHPUnit\Framework\TestCase;
use PsrJwt\Parser\Query;
use PsrJwt\Parser\ParserInterface;
use Psr\Http\Message\ServerRequestInterface;

class QueryTest extends TestCase
{
    /**
     * @covers PsrJwt\Parser\Query::__construct
     */
    public function testQuery(): void
    {
        $query = new Query('jwt');

        $this->assertInstanceOf(Query::class, $query);
        $this->assertInstanceOf(ParserInterface::class, $query);
    }

    /**
     * @covers PsrJwt\Parser\Query::parse
     * @uses PsrJwt\Parser\Query::__construct
     */
    public function testParse(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getQueryParams')
            ->willReturn(['jwt' => 'abc.def.ghi']);

        $query = new Query('jwt');
        $result = $query->parse($request);

        $this->assertSame('abc.def.ghi', $result);
    }
}
