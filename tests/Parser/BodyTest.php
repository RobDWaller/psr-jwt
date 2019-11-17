<?php

namespace Tests\Parser;

use PHPUnit\Framework\TestCase;
use PsrJwt\Parser\Body;
use PsrJwt\Parser\ParserInterface;
use Psr\Http\Message\ServerRequestInterface;
use Mockery as m;
use ReflectionMethod;

class BodyTest extends TestCase
{
    /**
     * @covers PsrJwt\Parser\Body::__construct
     */
    public function testBody()
    {
        $body = new Body('jwt');

        $this->assertInstanceOf(Body::class, $body);
        $this->assertInstanceOf(ParserInterface::class, $body);
    }

    /**
     * @covers PsrJwt\Parser\Body::parse
     * @uses PsrJwt\Parser\Body::__construct
     */
    public function testParse()
    {
        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getParsedBody')
            ->once()
            ->andReturn(['jwt' => 'abc.def.ghi']);

        $body = new Body('jwt');
        $result = $body->parse($request);

        $this->assertSame('abc.def.ghi', $result);
    }

    /**
     * @covers PsrJwt\Parser\Body::parse
     * @uses PsrJwt\Parser\Body
     */
    public function testParseObject()
    {
        $object = new \stdClass();
        $object->jwt = 'abc.def.ghi';

        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getParsedBody')
            ->twice()
            ->andReturn($object);

        $body = new Body('jwt');
        $result = $body->parse($request);

        $this->assertSame('abc.def.ghi', $result);
    }

    /**
     * @covers PsrJwt\Parser\Body::parse
     * @uses PsrJwt\Parser\Body
     */
    public function testParseString()
    {
        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getParsedBody')
            ->twice()
            ->andReturn('hello');

        $body = new Body('jwt');
        $result = $body->parse($request);

        $this->assertSame('', $result);
    }

    /**
     * @covers PsrJwt\Parser\Body::parseBodyObject
     * @uses PsrJwt\Parser\Body::__construct
     */
    public function testParseBodyObject()
    {
        $object = new \stdClass();
        $object->jwt = 'abc.def.ghi';

        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getParsedBody')
            ->once()
            ->andReturn($object);

        $body = new Body('jwt');

        $method = new ReflectionMethod(Body::class, 'parseBodyObject');
        $method->setAccessible(true);
        $result = $method->invokeArgs($body, [$request]);

        $this->assertSame('abc.def.ghi', $result);
    }

    /**
     * @covers PsrJwt\Parser\Body::parseBodyObject
     * @uses PsrJwt\Parser\Body::__construct
     */
    public function testParseBodyObjectNoKey()
    {
        $object = new \stdClass();

        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getParsedBody')
            ->once()
            ->andReturn($object);

        $body = new Body('jwt');

        $method = new ReflectionMethod(Body::class, 'parseBodyObject');
        $method->setAccessible(true);
        $result = $method->invokeArgs($body, [$request]);

        $this->assertSame('', $result);
    }

    /**
     * @covers PsrJwt\Parser\Body::parseBodyObject
     * @uses PsrJwt\Parser\Body::__construct
     */
    public function testParseBodyObjectNoObject()
    {
        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getParsedBody')
            ->once()
            ->andReturn([]);

        $body = new Body('jwt');

        $method = new ReflectionMethod(Body::class, 'parseBodyObject');
        $method->setAccessible(true);
        $result = $method->invokeArgs($body, [$request]);

        $this->assertSame('', $result);
    }

    public function tearDown()
    {
        m::close();
    }
}
