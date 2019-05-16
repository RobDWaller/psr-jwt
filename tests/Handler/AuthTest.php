<?php

namespace Tests\Handler;

use PHPUnit\Framework\TestCase;
use PsrJwt\Handler\Auth;
use PsrJwt\Auth\Authenticate;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use PsrJwt\Factory\Jwt;
use Mockery as m;

class AuthTest extends TestCase
{
    /**
     * @covers PsrJwt\Handler\Auth::__construct
     * @uses PsrJwt\Auth\Authenticate
     */
    public function testAuthHandler()
    {
        $auth = new Auth('secret', 'tokenKey', 'body');

        $this->assertInstanceOf(Auth::class, $auth);
        $this->assertInstanceOf(Authenticate::class, $auth);
        $this->assertInstanceOf(RequestHandlerInterface::class, $auth);
    }

    /**
     * @covers PsrJwt\Handler\Auth::handle
     * @uses PsrJwt\Handler\Auth::__construct
     * @uses PsrJwt\Factory\Jwt
     * @uses PsrJwt\Auth\Authenticate
     * @uses PsrJwt\Auth\Auth
     * @uses PsrJwt\Validation\Validate
     * @uses PsrJwt\Parser\Bearer
     * @uses PsrJwt\Parser\Cookie
     * @uses PsrJwt\Parser\Body
     * @uses PsrJwt\Parser\Query
     * @uses PsrJwt\Parser\Parse
     */
    public function testAuthenticateOk()
    {
        $jwt = Jwt::builder();
        $token = $jwt->setSecret('Secret123!456$')
            ->setIssuer('localhost')
            ->build()
            ->getToken();

        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getCookieParams')
            ->once()
            ->andReturn(['foo' => 'bar']);
        $request->shouldReceive('getQueryParams')
            ->once()
            ->andReturn(['jwt' => $token]);
        $request->shouldReceive('getParsedBody')
            ->twice()
            ->andReturn([]);
        $request->shouldReceive('getHeader')
            ->with('authorization')
            ->once()
            ->andReturn([]);

        $auth = new Auth('Secret123!456$', 'jwt', '<h1>Ok</h1>');

        $result = $auth->handle($request);

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertSame(200, $result->getStatusCode());
        $this->assertSame('Ok', $result->getReasonPhrase());
        $this->assertSame('<h1>Ok</h1>', $result->getBody()->__toString());
    }

    public function tearDown()
    {
        m::close();
    }
}
