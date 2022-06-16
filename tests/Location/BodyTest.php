<?php

declare(strict_types=1);

namespace Tests\Location;

use PHPUnit\Framework\TestCase;
use PsrJwt\Location\Body;
use PsrJwt\Location\LocationInterface;
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
        $this->assertInstanceOf(LocationInterface::class, $body);
    }

    /**
     * @covers PsrJwt\Parser\Body::parse
     * @uses PsrJwt\Parser\Body::__construct
     */
    public function testFind(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn(['jwt' => 'abc.def.ghi']);

        $body = new Body('jwt');
        $result = $body->find($request);

        $this->assertSame('abc.def.ghi', $result);
    }

    /**
     * @covers PsrJwt\Parser\Body::parse
     * @uses PsrJwt\Parser\Body
     */
    public function testFindObject(): void
    {
        $object = new \stdClass();
        $object->jwt = 'abc.def.ghi';

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn($object);

        $body = new Body('jwt');
        $result = $body->find($request);

        $this->assertSame('abc.def.ghi', $result);
    }

    /**
     * @covers PsrJwt\Parser\Body::parse
     * @uses PsrJwt\Parser\Body
     */
    public function testFindString(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn('hello');

        $body = new Body('jwt');
        $result = $body->find($request);

        $this->assertSame('', $result);
    }
}