<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use PsrJwt\JwtAuthInvokable;
use PsrJwt\Factory\Jwt;
use PsrJwt\Auth\Authenticate;
use PsrJwt\JwtAuthException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionMethod;
use Mockery as m;
use stdClass;

class JwtAuthInvokableTest extends TestCase
{
    /**
     * @covers PsrJwt\JwtAuthInvokable::__construct
     * @uses PsrJwt\JwtAuthHandler::__construct
     */
    public function testJwtAuthInokable()
    {
        $auth = new Authenticate('jwt', 'secret');

        $invokable = new JwtAuthInvokable($auth);

        $this->assertInstanceOf(JwtAuthInvokable::class, $invokable);
    }

    /**
     * @covers PsrJwt\JwtAuthInvokable::__invoke
     * @uses PsrJwt\JwtAuthInvokable::__construct
     * @uses PsrJwt\JwtAuthHandler
     * @uses PsrJwt\Factory\Jwt
     * @uses PsrJwt\Validation\Validate
     * @uses PsrJwt\Parser\Parse
     * @uses PsrJwt\Parser\Bearer
     */
    public function testInvoke()
    {
        $jwt = Jwt::builder();
        $token = $jwt->setSecret('Secret123!456$')
            ->setIssuer('localhost')
            ->setPayloadClaim('nbf', time() - 60)
            ->build()
            ->getToken();

        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getHeader')
            ->with('authorization')
            ->once()
            ->andReturn(['Bearer ' . $token]);

        $response = m::mock(ResponseInterface::class);

        $next = function($request, $response) {
            return $response;
        };

        $auth = new Authenticate('jwt', 'Secret123!456$');

        $invokable = new JwtAuthInvokable($auth);

        $result = $invokable($request, $response, $next);

        $this->assertInstanceOf(ResponseInterface::class, $result);
    }

    /**
     * @covers PsrJwt\JwtAuthInvokable::__invoke
     * @uses PsrJwt\JwtAuthInvokable
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
    public function testInvokeFail()
    {
        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getServerParams')
            ->once()
            ->andReturn(['jwt' => 'abc.abc.abc']);
        $request->shouldReceive('getCookieParams')
            ->once()
            ->andReturn(['hello' => 'world']);
        $request->shouldReceive('getQueryParams')
            ->once()
            ->andReturn(['car' => 'park']);
        $request->shouldReceive('getParsedBody')
            ->twice()
            ->andReturn(['gary' => 'barlow']);
        $request->shouldReceive('getHeader')
            ->with('authorization')
            ->once()
            ->andReturn([]);

        $response = m::mock(ResponseInterface::class);

        $next = function($request, $response) {
            return $response;
        };

        $auth = new Authenticate('jwt', 'secret');

        $invokable = new JwtAuthInvokable($auth);

        $result = $invokable($request, $response, $next);

        $this->assertSame(401, $result->getStatusCode());
        $this->assertSame('Unauthorized: Signature is invalid.', $result->getReasonPhrase());
    }

    public function tearDown() {
        m::close();
    }
}
