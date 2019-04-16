<?php

namespace Test\Parser;

use PHPUnit\Framework\TestCase;
use PsrJwt\Parser\Query;
use PsrJwt\Parser\ParserInterface;
use Psr\Http\Message\ServerRequestInterface;
use Mockery as m;

class QueryTest extends TestCase
{
    public function testQuery()
    {
        $query = new Query(['token_key' => 'jwt']);

        $this->assertInstanceOf(Query::class, $query);
        $this->assertInstanceOf(ParserInterface::class, $query);
    }

    public function testParse()
    {
        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getQueryParams')
            ->once()
            ->andReturn(['jwt' => 'abc.def.ghi']);

        $query = new Query(['token_key' => 'jwt']);
        $result = $query->parse($request);

        $this->assertSame('abc.def.ghi', $result);
    }

    public function tearDown()
    {
        m::close();
    }
}
