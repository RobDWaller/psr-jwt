<?php

namespace Tests\Helper;

use PHPUnit\Framework\TestCase;
use PsrJwt\Helper\Request;
use PsrJwt\Parser\Parse;
use ReallySimpleJWT\Parsed;
use Psr\Http\Message\ServerRequestInterface;
use Mockery as m;

class RequestTest extends TestCase
{
    private const TOKEN = 'eyJjdHkiOiJKV1QiLCJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.' .
        'eyJpc3MiOiJmYWtlcnMudGVzdCIsImF1ZCI6Imh0dHA6XC9cL2Zha2Vycy50ZXN0IiwiZXh' . 
        'wIjoxNTczNzE2NDA3LCJpYXQiOjE1NzM3MTU1MDcsInVzZXJfaWQiOiIzMTM4NjE2MiJ9.' . 
        '9Es0wWdByOQAU8WfcufxgRa9GEYwLefhRzclwWcgVCQ';

    public function testRequest()
    {
        $request = new Request();

        $this->assertInstanceOf(Request::class, $request);
    }

    public function testGetParsedToken()
    {
        $httpRequest = m::mock(ServerRequestInterface::class);
        $httpRequest->shouldReceive('getHeader')
            ->once()
            ->andReturn(['Bearer ' . self::TOKEN]);

        $request = new Request();

        $this->assertInstanceOf(
            Parsed::class, 
            $request->getParsedToken($httpRequest, 'jwt')
        );
    }

    public function tearDown(): void
    {
        m::close();
    }
}