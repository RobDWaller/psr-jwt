<?php

declare(strict_types=1);

namespace Tests\Handler;

use PHPUnit\Framework\TestCase;
use PsrJwt\Handler\Html;
use PsrJwt\Auth\Authorise;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use PsrJwt\Factory\Jwt;
use PsrJwt\Handler\Config;
use PsrJwt\Retrieve;
use PsrJwt\Location\Bearer;

class HtmlTest extends TestCase
{
    /**
     * @covers PsrJwt\Handler\Html::__construct
     * @uses PsrJwt\Auth\Authorise
     */
    public function testHtmlHandler(): void
    {
        $config = new Config('secret', 'tokenKey', 'body');
        $handler = new Html($config, new Retrieve([new Bearer()]), new Authorise());

        $this->assertInstanceOf(Html::class, $handler);
        $this->assertInstanceOf(RequestHandlerInterface::class, $handler);
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
    public function testAuthoriseOk(): void
    {
        $jwt = $jwt = new Jwt();
        $jwt = $jwt->builder('Secret123!456$');
        $token = $jwt->setIssuer('localhost')
            ->setExpiration(time() + 20)
            ->setNotBefore(time() - 20)
            ->build()
            ->getToken();

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getHeader')
            ->with('authorization')
            ->willReturn([]);
        $request->expects($this->once())
            ->method('getCookieParams')
            ->willReturn(['foo' => 'bar']);
        $request->expects($this->exactly(2))
            ->method('getParsedBody')
            ->willReturn([]);
        $request->expects($this->once())
            ->method('getQueryParams')
            ->willReturn(['jwt' => $token]);

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
    public function testAuthoriseNoBody(): void
    {
        $jwt = $jwt = new Jwt();
        $jwt = $jwt->builder('Secret123!456$');
        $token = $jwt->setIssuer('localhost')
            ->setExpiration(time() + 20)
            ->setNotBefore(time() - 20)
            ->build()
            ->getToken();

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getHeader')
            ->with('authorization')
            ->willReturn([]);
        $request->expects($this->once())
            ->method('getCookieParams')
            ->willReturn(['foo' => 'bar']);
        $request->expects($this->exactly(2))
            ->method('getParsedBody')
            ->willReturn([]);
        $request->expects($this->once())
            ->method('getQueryParams')
            ->willReturn(['jwt' => $token]);

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
    public function testAuthoriseBadRequest(): void
    {
        $jwt = $jwt = new Jwt();
        $jwt = $jwt->builder('Secret123!456$');
        $token = $jwt->setIssuer('localhost')
            ->setExpiration(time() + 20)
            ->setNotBefore(time() - 20)
            ->build()
            ->getToken();

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getHeader')
            ->with('authorization')
            ->willReturn([]);
        $request->expects($this->once())
            ->method('getCookieParams')
            ->willReturn(['foo' => 'bar']);
        $request->expects($this->exactly(2))
            ->method('getParsedBody')
            ->willReturn(['jwt' => $token]);
        $request->expects($this->once())
            ->method('getQueryParams')
            ->willReturn([]);

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
    public function testAuthoriseUnauthorized(): void
    {
        $jwt = $jwt = new Jwt();
        $jwt = $jwt->builder('Secret123!456$');
        $token = $jwt->setIssuer('localhost')
            ->setExpiration(time() + 20)
            ->setNotBefore(time() - 20)
            ->build()
            ->getToken();

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getHeader')
            ->with('authorization')
            ->willReturn([]);
        $request->expects($this->once())
            ->method('getCookieParams')
            ->willReturn(['foo' => $token]);

        $auth = new Html('1Secret23!456$', 'foo', '<h1>Fail!</h1>');

        $result = $auth->handle($request);

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertSame(401, $result->getStatusCode());
        $this->assertSame('Unauthorized: Signature is invalid.', $result->getReasonPhrase());
        $this->assertSame('<h1>Fail!</h1>', $result->getBody()->__toString());
    }
}
