<?php

namespace Tests\Auth;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use PsrJwt\Auth\Authenticate;
use PsrJwt\Auth\Auth;
use PsrJwt\Factory\Jwt;
use Mockery as m;

class AuthenticateTest extends TestCase
{
    public function testAuthenticate()
    {
        $auth = new Authenticate('jwt', 'secret');
        $this->assertInstanceOf(Authenticate::class, $auth);
    }

    /**
     * @covers PsrJwt\JwtAuthHandler::handle
     * @uses PsrJwt\JwtAuthHandler
     * @uses PsrJwt\Factory\Jwt
     * @uses PsrJwt\Validation\Validate
     * @uses PsrJwt\Parser\Parse
     * @uses PsrJwt\Parser\Body
     * @uses PsrJwt\Parser\Bearer
     * @uses PsrJwt\Parser\Server
     * @uses PsrJwt\Parser\Query
     * @uses PsrJwt\Parser\Cookie
     */
    public function testAuthenticateOk()
    {
        $jwt = Jwt::builder();
        $token = $jwt->setSecret('Secret123!456$')
            ->setIssuer('localhost')
            ->build()
            ->getToken();

        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getServerParams')
            ->once()
            ->andReturn(['jwt' => $token]);
        $request->shouldReceive('getCookieParams')
            ->once()
            ->andReturn(['foo' => 'bar']);
        $request->shouldReceive('getQueryParams')
            ->once()
            ->andReturn(['hello' => 'world']);
        $request->shouldReceive('getParsedBody')
            ->twice()
            ->andReturn([]);
        $request->shouldReceive('getHeader')
            ->with('authorization')
            ->once()
            ->andReturn([]);

        $authenticate = new Authenticate('jwt', 'Secret123!456$');

        $result = $authenticate->authenticate($request);

        $this->assertInstanceOf(Auth::class, $result);
        $this->assertSame(200, $result->getCode());
        $this->assertSame('Ok', $result->getMessage());
    }

    public function tearDown()
    {
        m::close();
    }
}
