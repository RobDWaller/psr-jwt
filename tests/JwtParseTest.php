<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use PsrJwt\JwtParse;
use Psr\Http\Message\ServerRequestInterface;
use PsrJwt\Parser\Bearer;
use ReflectionMethod;
use Mockery as m;
use stdClass;

class JwtParseTest extends TestCase
{
    /**
     * @covers PsrJwt\JwtParse::__construct
     */
    public function testJwtParse()
    {
        $parse = new JwtParse(['token_key' => 'jwt']);
        $this->assertInstanceOf(JwtParse::class, $parse);
    }

    /**
     * @covers PsrJwt\JwtParse::findToken
     * @uses PsrJwt\JwtParse
     */
    public function testFindToken()
    {
        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getHeader')
            ->with('authorization')
            ->once()
            ->andReturn(['bearer abc.def.ghi']);

        $parse = new JwtParse(['token_key' => 'jwt']);
        $parse->addParser(Bearer::class);

        $result = $parse->findToken($request);

        $this->assertSame('abc.def.ghi', $result);
    }

    public function tearDown() {
        m::close();
    }
}
