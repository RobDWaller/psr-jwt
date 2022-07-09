<?php

declare(strict_types=1);

namespace Tests\Handler;

use PHPUnit\Framework\TestCase;
use PsrJwt\Handler\Json;
use PsrJwt\Auth\Authorise;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use PsrJwt\Factory\Jwt;
use PsrJwt\Handler\Config;
use PsrJwt\Factory\Retriever;

class JsonTest extends TestCase
{
    /**
     * @covers PsrJwt\Handler\Json::__construct
     */
    public function testJsonHandler(): void
    {
        $config = new Config('secret', ['body']);
        $handler = new Json(
            $config,
            Retriever::make('tokenkey'),
            new Authorise()
        );

        $this->assertInstanceOf(Json::class, $handler);
        $this->assertInstanceOf(RequestHandlerInterface::class, $handler);
    }

    /**
     * @covers PsrJwt\Handler\Json::handle
     */
    public function testAuthoriseOk(): void
    {
        $jwt = $jwt = new Jwt();
        $jwt = $jwt->builder('Secret123!456$');
        $token = $jwt->setExpiration(time() + 10)
            ->setNotBefore(time() - 10)
            ->setIssuer('localhost')
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

        $config = new Config('Secret123!456$', ['Ok']);
        $handler = new Json(
            $config,
            Retriever::make('jwt'),
            new Authorise()
        );

        $result = $handler->handle($request);

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertSame(200, $result->getStatusCode());
        $this->assertSame('Ok', $result->getReasonPhrase());
        $this->assertSame('application/json', $result->getHeader('Content-Type')[0]);
        $this->assertSame(json_encode(['Ok']), $result->getBody()->__toString());
    }

    /**
     * @covers PsrJwt\Handler\Json::handle
     */
    public function testAuthoriseFail(): void
    {
        $jwt = $jwt = new Jwt();
        $jwt = $jwt->builder('Secret123!456$');
        $token = $jwt->setExpiration(time() + 10)
            ->setNotBefore(time() - 10)
            ->setIssuer('localhost')
            ->build()
            ->getToken();

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getHeader')
            ->with('authorization')
            ->willReturn(['Bearer ' . $token]);

        $config = new Config('Secret123!456', ['message' => 'Bad']);
        $handler = new Json(
            $config,
            Retriever::make('jwt'),
            new Authorise()
        );

        $result = $handler->handle($request);

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
