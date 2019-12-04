<?php

namespace Tests\Handler;

use PHPUnit\Framework\TestCase;
use PsrJwt\Handler\Html;
use PsrJwt\Auth\Authorise;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use PsrJwt\Factory\Jwt;
use Mockery as m;

class HtmlTest extends TestCase
{
    /**
     * @covers PsrJwt\Handler\Html::__construct
     * @uses PsrJwt\Auth\Authorise
     */
    public function testAuthHandler()
    {
        $auth = new Html('secret', 'tokenKey', 'body');

        $this->assertInstanceOf(Html::class, $auth);
        $this->assertInstanceOf(Authorise::class, $auth);
        $this->assertInstanceOf(RequestHandlerInterface::class, $auth);
    }

    /**
     * @covers PsrJwt\Handler\Html::handle
     * @uses PsrJwt\Handler\Html::__construct
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

        $auth = new Html('Secret123!456$', 'jwt', '<h1>Ok</h1>');

        $result = $auth->handle($request);

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertSame(200, $result->getStatusCode());
        $this->assertSame('Ok', $result->getReasonPhrase());
        $this->assertSame('text/html', $result->getHeader('Content-Type')[0]);
        $this->assertSame('<h1>Ok</h1>', $result->getBody()->__toString());
    }

    /**
     * @covers PsrJwt\Handler\Html::handle
     * @uses PsrJwt\Handler\Html::__construct
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
    public function testAuthoriseNoBody()
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

        $auth = new Html('Secret123!456$', 'jwt', '');

        $result = $auth->handle($request);

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertSame(200, $result->getStatusCode());
        $this->assertSame('Ok', $result->getReasonPhrase());
        $this->assertEmpty($result->getBody()->__toString());
    }

    /**
     * @covers PsrJwt\Handler\Html::handle
     * @uses PsrJwt\Handler\Html::__construct
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
     * @uses PsrJwt\Parser\ParseException
     */
    public function testAuthoriseBadRequest()
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
            ->andReturn(['jwt' => $token]);
        $request->shouldReceive('getQueryParams')
            ->once()
            ->andReturn([]);

        $auth = new Html('Secret123!456$', '', '<h1>Fail!</h1>');

        $result = $auth->handle($request);

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertSame(400, $result->getStatusCode());
        $this->assertSame('Bad Request: JSON Web Token not set in request.', $result->getReasonPhrase());
        $this->assertSame('<h1>Fail!</h1>', $result->getBody()->__toString());
    }

    /**
     * @covers PsrJwt\Handler\Html::handle
     * @uses PsrJwt\Handler\Html::__construct
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
    public function testAuthoriseUnauthorized()
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
            ->andReturn(['foo' => $token]);

        $auth = new Html('1Secret23!456$', 'foo', '<h1>Fail!</h1>');

        $result = $auth->handle($request);

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertSame(401, $result->getStatusCode());
        $this->assertSame('Unauthorized: Signature is invalid.', $result->getReasonPhrase());
        $this->assertSame('<h1>Fail!</h1>', $result->getBody()->__toString());
    }

    public function tearDown()
    {
        m::close();
    }
}
