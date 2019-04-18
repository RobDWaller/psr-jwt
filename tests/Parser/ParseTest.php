<?php

namespace Tests\Parser;

use PHPUnit\Framework\TestCase;
use PsrJwt\Parser\Parse;
use Psr\Http\Message\ServerRequestInterface;
use PsrJwt\Parser\Bearer;
use PsrJwt\Parser\Cookie;
use ReflectionMethod;
use Mockery as m;
use stdClass;

class ParseTest extends TestCase
{
    /**
     * @covers PsrJwt\Parser\Parse::__construct
     */
    public function testParse()
    {
        $parse = new Parse(['token_key' => 'jwt']);
        $this->assertInstanceOf(Parse::class, $parse);
    }

    /**
     * @covers PsrJwt\Parser\Parse::findToken
     * @uses PsrJwt\Parser\Parse
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

    /**
     * @covers PsrJwt\Parser\Parse::addParser
     * @covers PsrJwt\Parser\Parse::getParsers
     * @uses PsrJwt\Parser\Parse
     */
    public function testAddParser()
    {
        $parse = new Parse(['token_key' => 'jwt']);
        $parse->addParser(Bearer::class);
        $parse->addParser(Cookie::class);

        $result = $parse->getParsers();

        $this->assertCount(2, $result);
        $this->assertSame(Bearer::class, $result[0]);
        $this->assertSame(Cookie::class, $result[1]);
    }

    /**
     * @covers PsrJwt\Parser\Parse::addParser
     * @covers PsrJwt\Parser\Parse::getParsers
     * @uses PsrJwt\Parser\Parse
     */
    public function testFindTokenFail()
    {
        $request = m::mock(ServerRequestInterface::class);

        $parse = new Parse(['token_key' => 'jwt']);
        $result = $parse->findToken($request);

        $this->assertEmpty($result);
    }

    public function tearDown() {
        m::close();
    }
}
