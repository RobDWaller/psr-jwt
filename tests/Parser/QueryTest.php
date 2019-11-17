<?php

namespace Tests\Parser;

use PHPUnit\Framework\TestCase;
use PsrJwt\Parser\Query;
use PsrJwt\Parser\ParserInterface;
use Psr\Http\Message\ServerRequestInterface;
use Mockery as m;

class QueryTest extends TestCase
{
    /**
     * @covers PsrJwt\Parser\Query::__construct
     */
    public function testQuery()
    {
        $query = new Query('jwt');

        $this->assertInstanceOf(Query::class, $query);
        $this->assertInstanceOf(ParserInterface::class, $query);
    }

    /**
     * @covers PsrJwt\Parser\Query::parse
     * @uses PsrJwt\Parser\Query::__construct
     */
    public function testParse()
    {
        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getQueryParams')
            ->once()
            ->andReturn(['jwt' => 'abc.def.ghi']);

        $query = new Query('jwt');
        $result = $query->parse($request);

        $this->assertSame('abc.def.ghi', $result);
    }

    public function tearDown()
    {
        m::close();
    }
}
