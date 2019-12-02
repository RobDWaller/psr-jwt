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

    /**
     * @covers PsrJwt\Helper\Request
     */
    public function testRequest()
    {
        $request = new Request();

        $this->assertInstanceOf(Request::class, $request);
    }

    /**
     * @covers PsrJwt\Helper\Request::getParsedToken
     * @uses PsrJwt\Factory\Jwt
     * @uses PsrJwt\Parser\Bearer
     * @uses PsrJwt\Parser\Body
     * @uses PsrJwt\Parser\Cookie
     * @uses PsrJwt\Parser\Parse
     * @uses PsrJwt\Parser\Query
     * @uses PsrJwt\Parser\Request
     */
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

    /**
     * @covers PsrJwt\Helper\Request::getTokenHeader
     * @uses PsrJwt\Helper\Request
     * @uses PsrJwt\Factory\Jwt
     * @uses PsrJwt\Parser\Bearer
     * @uses PsrJwt\Parser\Body
     * @uses PsrJwt\Parser\Cookie
     * @uses PsrJwt\Parser\Parse
     * @uses PsrJwt\Parser\Query
     * @uses PsrJwt\Parser\Request
     */
    public function testGetTokenHeader()
    {
        $httpRequest = m::mock(ServerRequestInterface::class);
        $httpRequest->shouldReceive('getHeader')
            ->once()
            ->andReturn(['Bearer ' . self::TOKEN]);

        $request = new Request();

        $result = $request->getTokenHeader($httpRequest, 'jwt');

        $this->assertIsArray($result);
        $this->assertSame($result['typ'], 'JWT');
    }

    /**
     * @covers PsrJwt\Helper\Request::getTokenPayload
     * @uses PsrJwt\Helper\Request
     * @uses PsrJwt\Factory\Jwt
     * @uses PsrJwt\Parser\Bearer
     * @uses PsrJwt\Parser\Body
     * @uses PsrJwt\Parser\Cookie
     * @uses PsrJwt\Parser\Parse
     * @uses PsrJwt\Parser\Query
     * @uses PsrJwt\Parser\Request
     */
    public function testGetTokenPayload()
    {
        $httpRequest = m::mock(ServerRequestInterface::class);
        $httpRequest->shouldReceive('getHeader')
            ->once()
            ->andReturn(['Bearer ' . self::TOKEN]);

        $request = new Request();

        $result = $request->getTokenPayload($httpRequest, 'jwt');

        $this->assertIsArray($result);
        $this->assertSame($result['user_id'], '31386162');
    }

    public function tearDown(): void
    {
        m::close();
    }
}
