<?php

namespace Tests\Handler;

use PHPUnit\Framework\TestCase;
use PsrJwt\Handler\Json;
use PsrJwt\Auth\Authorise;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use PsrJwt\Factory\Jwt;
use Mockery as m;

class JsonTest extends TestCase
{
    /**
     * @covers PsrJwt\Handler\Json::__construct
     * @uses PsrJwt\Auth\Authorise
     */
    public function testJsonAuthHandler()
    {
        $auth = new Json('secret', 'tokenKey', ['body']);

        $this->assertInstanceOf(Json::class, $auth);
        $this->assertInstanceOf(Authorise::class, $auth);
        $this->assertInstanceOf(RequestHandlerInterface::class, $auth);
    }

    /**
     * @covers PsrJwt\Handler\Json::handle
     * @uses PsrJwt\Handler\Json::__construct
     * @uses PsrJwt\Factory\Jwt
     * @uses PsrJwt\Auth\Authorise
     * @uses PsrJwt\Auth\Auth
     * @uses PsrJwt\Validation\Validate
     * @uses PsrJwt\Parser\Bearer
     * @uses PsrJwt\Parser\Cookie
     * @uses PsrJwt\Parser\Body
     * @uses PsrJwt\Parser\Query
     * @uses PsrJwt\Parser\Parse
     * @uses PsrJwt\Parser\Bearer
     * @uses PsrJwt\Parser\Request
     */
    public function testAuthoriseOk()
    {
        $jwt = $jwt = new Jwt();
        $jwt = $jwt->builder();
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

        $auth = new Json('Secret123!456$', 'jwt', ['Ok']);

        $result = $auth->handle($request);

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertSame(200, $result->getStatusCode());
        $this->assertSame('Ok', $result->getReasonPhrase());
        $this->assertSame('application/json', $result->getHeader('Content-Type')[0]);
        $this->assertSame(json_encode(['Ok']), $result->getBody()->__toString());
    }

    /**
     * @covers PsrJwt\Handler\Json::handle
     * @uses PsrJwt\Handler\Json::__construct
     * @uses PsrJwt\Factory\Jwt
     * @uses PsrJwt\Auth\Authorise
     * @uses PsrJwt\Auth\Auth
     * @uses PsrJwt\Validation\Validate
     * @uses PsrJwt\Parser\Bearer
     * @uses PsrJwt\Parser\Cookie
     * @uses PsrJwt\Parser\Body
     * @uses PsrJwt\Parser\Query
     * @uses PsrJwt\Parser\Parse
     * @uses PsrJwt\Parser\Request
     */
    public function testAuthoriseFail()
    {
        $jwt = $jwt = new Jwt();
        $jwt = $jwt->builder();
        $token = $jwt->setSecret('Secret123!456$')
            ->setIssuer('localhost')
            ->build()
            ->getToken();

        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getHeader')
            ->with('authorization')
            ->once()
            ->andReturn(['Bearer ' . $token]);

        $auth = new Json('Secret123!456', 'jwt', ['message' => 'Bad']);

        $result = $auth->handle($request);

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertSame(401, $result->getStatusCode());
        $this->assertSame('Unauthorized: Signature is invalid.', $result->getReasonPhrase());
        $this->assertSame('application/json', $result->getHeader('Content-Type')[0]);
        $this->assertSame(
            json_encode(['message' => 'Bad']),
            $result->getBody()->__toString()
        );
    }
}
