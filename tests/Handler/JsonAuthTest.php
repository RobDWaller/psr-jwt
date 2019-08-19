<?php

namespace Tests\Handler;

use PHPUnit\Framework\TestCase;
use PsrJwt\Handler\JsonAuth;
use PsrJwt\Auth\Authenticate;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use PsrJwt\Factory\Jwt;
use Mockery as m;

class JsonAuthTest extends TestCase
{
    /**
     * @covers PsrJwt\Handler\JsonAuth::__construct
     * @uses PsrJwt\Auth\Authenticate
     */
    public function testJsonAuthHandler()
    {
        $auth = new JsonAuth('secret', 'tokenKey', ['body']);

        $this->assertInstanceOf(JsonAuth::class, $auth);
        $this->assertInstanceOf(Authenticate::class, $auth);
        $this->assertInstanceOf(RequestHandlerInterface::class, $auth);
    }

    /**
     * @covers PsrJwt\Handler\JsonAuth::handle
     * @uses PsrJwt\Handler\JsonAuth::__construct
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
        $request->shouldReceive('getHeader')
            ->with('authorization')
            ->once()
            ->andReturn([]);
        $request->shouldReceive('getCookieParams')
            ->once()
            ->andReturn(['foo' => 'bar']);
        $request->shouldReceive('getParsedBody')
            ->twice()
            ->andReturn([]);
        $request->shouldReceive('getQueryParams')
            ->once()
            ->andReturn(['jwt' => $token]);

        $auth = new JsonAuth('Secret123!456$', 'jwt', ['Ok']);

        $result = $auth->handle($request);

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertSame(200, $result->getStatusCode());
        $this->assertSame('Ok', $result->getReasonPhrase());
        $this->assertSame('application/json', $result->getHeader('Content-Type')[0]);
        $this->assertSame(json_encode(['message' => 'Ok', 'Ok']), $result->getBody()->__toString());
    }

    /**
     * @covers PsrJwt\Handler\JsonAuth::handle
     * @uses PsrJwt\Handler\JsonAuth::__construct
     * @uses PsrJwt\Factory\Jwt
     * @uses PsrJwt\Auth\Authenticate
     * @uses PsrJwt\Auth\Auth
     * @uses PsrJwt\Validation\Validate
     * @uses PsrJwt\Parser\Bearer
     * @uses PsrJwt\Parser\Parse
     */
    public function testAuthenticateFail()
    {
        $jwt = Jwt::builder();
        $token = $jwt->setSecret('Secret123!456$')
            ->setIssuer('localhost')
            ->build()
            ->getToken();

        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getHeader')
            ->with('authorization')
            ->once()
            ->andReturn(['Bearer ' . $token]);

        $auth = new JsonAuth('Secret123!456', 'jwt', []);

        $result = $auth->handle($request);

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertSame(401, $result->getStatusCode());
        $this->assertSame('Unauthorized: Signature is invalid.', $result->getReasonPhrase());
        $this->assertSame('application/json', $result->getHeader('Content-Type')[0]);
        $this->assertSame(
            json_encode(['message' => 'Unauthorized: Signature is invalid.']),
            $result->getBody()->__toString()
        );
    }
}
