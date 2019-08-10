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
     * @covers PsrJwt\Handler\Auth::__construct
     * @uses PsrJwt\Auth\Authenticate
     */
    public function testJsonAuthHandler()
    {
        $auth = new JsonAuth('secret', 'tokenKey', 'body');

        $this->assertInstanceOf(JsonAuth::class, $auth);
        $this->assertInstanceOf(Authenticate::class, $auth);
        $this->assertInstanceOf(RequestHandlerInterface::class, $auth);
    }
}
