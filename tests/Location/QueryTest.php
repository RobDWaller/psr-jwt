<?php

declare(strict_types=1);

namespace Tests\Location;

use PHPUnit\Framework\TestCase;
use PsrJwt\Location\Query;
use PsrJwt\Location\LocationInterface;
use Psr\Http\Message\ServerRequestInterface;

class QueryTest extends TestCase
{
    /**
     * @covers PsrJwt\Location\Query::__construct
     */
    public function testQuery(): void
    {
        $query = new Query('jwt');

        $this->assertInstanceOf(Query::class, $query);
        $this->assertInstanceOf(LocationInterface::class, $query);
    }

    /**
     * @covers PsrJwt\Location\Query::find
     * @uses PsrJwt\Location\Query::__construct
     */
    public function testFind(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getQueryParams')
            ->willReturn(['jwt' => 'abc.def.ghi']);

        $query = new Query('jwt');
        $result = $query->find($request);

        $this->assertSame('abc.def.ghi', $result);
    }
}
