<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use PsrJwt\Helper\Parse;
use Psr\Http\Message\ServerRequestInterface;
use PsrJwt\Parser\Bearer;
use ReflectionMethod;
use Mockery as m;
use stdClass;

class ParseTest extends TestCase
{
    /**
     * @covers PsrJwt\Helper\Parse::__construct
     */
    public function testParse()
    {
        $parse = new Parse(['token_key' => 'jwt']);
        $this->assertInstanceOf(Parse::class, $parse);
    }

    /**
     * @covers PsrJwt\Helper\Parse::findToken
     * @uses PsrJwt\Helper\Parse
     * @uses PsrJwt\Parser\Bearer
     */
    public function testFindToken()
    {
        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getHeader')
            ->with('authorization')
            ->once()
            ->andReturn(['Bearer abc.def.ghi']);

        $parse = new Parse(['token_key' => 'jwt']);
        $parse->addParser(Bearer::class);

        $result = $parse->findToken($request);

        $this->assertSame('abc.def.ghi', $result);
    }

    public function tearDown() {
        m::close();
    }
}
