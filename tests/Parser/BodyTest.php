<?php

declare(strict_types=1);

namespace Tests\Parser;

use PHPUnit\Framework\TestCase;
use PsrJwt\Parser\Body;
use PsrJwt\Parser\ParserInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionMethod;

class BodyTest extends TestCase
{
    /**
     * @covers PsrJwt\Parser\Body::__construct
     */
    public function testBody(): void
    {
        $body = new Body('jwt');

        $this->assertInstanceOf(Body::class, $body);
        $this->assertInstanceOf(ParserInterface::class, $body);
    }

    /**
     * @covers PsrJwt\Parser\Body::parse
     * @uses PsrJwt\Parser\Body::__construct
     */
    public function testParse(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn(['jwt' => 'abc.def.ghi']);

        $body = new Body('jwt');
        $result = $body->parse($request);

        $this->assertSame('abc.def.ghi', $result);
    }

    /**
     * @covers PsrJwt\Parser\Body::parse
     * @uses PsrJwt\Parser\Body
     */
    public function testParseObject(): void
    {
        $object = new \stdClass();
        $object->jwt = 'abc.def.ghi';

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->exactly(2))
            ->method('getParsedBody')
            ->willReturn($object);

        $body = new Body('jwt');
        $result = $body->parse($request);

        $this->assertSame('abc.def.ghi', $result);
    }

    /**
     * @covers PsrJwt\Parser\Body::parse
     * @uses PsrJwt\Parser\Body
     */
    public function testParseString(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->exactly(2))
            ->method('getParsedBody')
            ->willReturn('hello');

        $body = new Body('jwt');
        $result = $body->parse($request);

        $this->assertSame('', $result);
    }

    /**
     * @covers PsrJwt\Parser\Body::parseBodyObject
     * @uses PsrJwt\Parser\Body::__construct
     */
    public function testParseBodyObject(): void
    {
        $object = new \stdClass();
        $object->jwt = 'abc.def.ghi';

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn($object);

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
    public function testParseBodyObjectNoKey(): void
    {
        $object = new \stdClass();

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn($object);

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
    public function testParseBodyObjectNoObject(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn([]);

        $body = new Body('jwt');

        $method = new ReflectionMethod(Body::class, 'parseBodyObject');
        $method->setAccessible(true);
        $result = $method->invokeArgs($body, [$request]);

        $this->assertSame('', $result);
    }
}
