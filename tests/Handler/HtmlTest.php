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
use PsrJwt\Factory\Retriever;

class HtmlTest extends TestCase
{
    /**
     * @covers PsrJwt\Handler\Html::__construct
     * @uses PsrJwt\Auth\Authorise
     */
    public function testHtmlHandler(): void
    {
        $config = new Config('secret', 'body');
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
    public function testHandlerOk(): void
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
        $request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn([]);
        $request->expects($this->once())
            ->method('getQueryParams')
            ->willReturn(['jwt' => $token]);

        $config = new Config('Secret123!456$', '<h1>Ok</h1>');
        $handler = new Html(
            $config,
            Retriever::make('jwt'),
            new Authorise()
        );

        $result = $handler->handle($request);

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
    public function testHandlerNoBody(): void
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
        $request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn([]);
        $request->expects($this->once())
            ->method('getQueryParams')
            ->willReturn(['jwt' => $token]);

        $config = new Config('Secret123!456$', '');
        $handler = new Html(
            $config,
            Retriever::make('jwt'),
            new Authorise()
        );

        $result = $handler->handle($request);

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
    public function testHandlerBadRequest(): void
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
        $request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn(['jwt' => $token]);
        $request->expects($this->once())
            ->method('getQueryParams')
            ->willReturn([]);

        $config = new Config('Secret123!456$', '<h1>Fail!</h1>');
        $handler = new Html(
            $config,
            Retriever::make(''),
            new Authorise()
        );

        $result = $handler->handle($request);

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertSame(401, $result->getStatusCode());
        $this->assertSame('Unauthorized: JSON Web Token not set in request.', $result->getReasonPhrase());
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
    public function testHandlerUnauthorized(): void
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

        $config = new Config('1Secret23!456$', '<h1>Fail!</h1>');
        $handler = new Html(
            $config,
            Retriever::make('foo'),
            new Authorise()
        );

        $result = $handler->handle($request);

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertSame(401, $result->getStatusCode());
        $this->assertSame('Unauthorized: Signature is invalid.', $result->getReasonPhrase());
        $this->assertSame('<h1>Fail!</h1>', $result->getBody()->__toString());
    }
}
