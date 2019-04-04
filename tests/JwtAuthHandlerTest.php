<?php

namespace Test;

use PHPUnit\Framework\TestCase;
use PsrJwt\JwtAuthHandler;
use PsrJwt\Jwt;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionMethod;
use Mockery as m;
use stdClass;

class JwtAuthHandlerTest extends TestCase
{
    public function testJwtAuthHandler()
    {
        $handler = new JwtAuthHandler('jwt', 'secret');

        $this->assertInstanceOf(JwtAuthHandler::class, $handler);
        $this->assertInstanceOf(RequestHandlerInterface::class, $handler);
    }

    public function testJwtAuthHandlerResponse()
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

        $handler = new JwtAuthHandler('jwt', 'Secret123!456$');

        $result = $handler->handle($request);

        $this->assertInstanceOf(ResponseInterface::class, $result);
    }

    /**
     * @covers PsrJwt\JwtAuthHandler::hasJwt
     */
    public function testJwtAuthHandlerHasJwt()
    {
        $handler = new JwtAuthHandler('jwt', 'secret');

        $data['jwt'] = 'abc.abc.abc';
        $data['foo'] = 'bar';

        $method = new ReflectionMethod(JwtAuthHandler::class, 'hasJwt');
        $method->setAccessible(true);
        $result = $method->invokeArgs($handler, [$data]);

        $this->assertTrue($result);
    }

    /**
     * @covers PsrJwt\JwtAuthHandler::hasJwt
     */
    public function testJwtAuthHandlerHasJwtFalse()
    {
        $handler = new JwtAuthHandler('jwt', 'secret');

        $data['token'] = 'abc.abc.abc';
        $data['foo'] = 'bar';

        $method = new ReflectionMethod(JwtAuthHandler::class, 'hasJwt');
        $method->setAccessible(true);
        $result = $method->invokeArgs($handler, [$data]);

        $this->assertFalse($result);
    }

    /**
     * @covers PsrJwt\JwtAuthHandler::hasJwt
     */
    public function testJwtAuthHandlerHasJwtEmpty()
    {
        $handler = new JwtAuthHandler('jwt', 'secret');

        $data = [];

        $method = new ReflectionMethod(JwtAuthHandler::class, 'hasJwt');
        $method->setAccessible(true);
        $result = $method->invokeArgs($handler, [$data]);

        $this->assertFalse($result);
    }

    /**
     * @covers PsrJwt\JwtAuthHandler::getToken
     * @uses PsrJwt\JwtAuthHandler::hasJwt
     */
    public function testGetToken()
    {
        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getServerParams')
            ->once()
            ->andReturn(['jwt' => 'abc.def.ghi']);
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

        $handler = new JwtAuthHandler('jwt', 'secret');

        $method = new ReflectionMethod(JwtAuthHandler::class, 'getToken');
        $method->setAccessible(true);
        $result = $method->invokeArgs($handler, [$request]);

        $this->assertSame($result, 'abc.def.ghi');
    }

    /**
     * @covers PsrJwt\JwtAuth::getToken
     * @uses PsrJwt\JwtAuth::hasJwt
     */
    public function testGetTokenFromCookie()
    {
        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getServerParams')
            ->once()
            ->andReturn(['foo' => 'bar']);
        $request->shouldReceive('getCookieParams')
            ->once()
            ->andReturn(['jwt' => 'abc.def.ghi']);
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

        $handler = new JwtAuthHandler('jwt', 'secret');

        $method = new ReflectionMethod(JwtAuthHandler::class, 'getToken');
        $method->setAccessible(true);
        $result = $method->invokeArgs($handler, [$request]);

        $this->assertSame($result, 'abc.def.ghi');
    }

    /**
     * @covers PsrJwt\JwtAuth::getToken
     * @uses PsrJwt\JwtAuth::hasJwt
     */
    public function testGetTokenFromQuery()
    {
        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getServerParams')
            ->once()
            ->andReturn(['foo' => 'bar']);
        $request->shouldReceive('getCookieParams')
            ->once()
            ->andReturn(['hello' => 'world']);
        $request->shouldReceive('getQueryParams')
            ->once()
            ->andReturn(['jwt' => 'abc.def.ghi']);
        $request->shouldReceive('getParsedBody')
            ->twice()
            ->andReturn([]);
        $request->shouldReceive('getHeader')
            ->with('authorization')
            ->once()
            ->andReturn([]);

        $handler = new JwtAuthHandler('jwt', 'secret');

        $method = new ReflectionMethod(JwtAuthHandler::class, 'getToken');
        $method->setAccessible(true);
        $result = $method->invokeArgs($handler, [$request]);

        $this->assertSame($result, 'abc.def.ghi');
    }

    /**
     * @covers PsrJwt\JwtAuth::getToken
     * @uses PsrJwt\JwtAuth::hasJwt
     */
    public function testGetTokenFromBody()
    {
        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getServerParams')
            ->once()
            ->andReturn(['foo' => 'bar']);
        $request->shouldReceive('getCookieParams')
            ->once()
            ->andReturn(['hello' => 'world']);
        $request->shouldReceive('getQueryParams')
            ->once()
            ->andReturn(['car' => 'park']);
        $request->shouldReceive('getParsedBody')
            ->times(3)
            ->andReturn(['jwt' => 'abc.def.ghi']);
        $request->shouldReceive('getHeader')
            ->with('authorization')
            ->once()
            ->andReturn([]);

        $handler = new JwtAuthHandler('jwt', 'secret');

        $method = new ReflectionMethod(JwtAuthHandler::class, 'getToken');
        $method->setAccessible(true);
        $result = $method->invokeArgs($handler, [$request]);

        $this->assertSame($result, 'abc.def.ghi');
    }

    /**
     * @covers PsrJwt\JwtAuth::getToken
     * @uses PsrJwt\JwtAuth::hasJwt
     */
    public function testGetTokenFromBodyObject()
    {
        $object = new stdClass();
        $object->jwt = 'abc.def.ghi';

        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getServerParams')
            ->once()
            ->andReturn(['foo' => 'bar']);
        $request->shouldReceive('getCookieParams')
            ->once()
            ->andReturn(['hello' => 'world']);
        $request->shouldReceive('getQueryParams')
            ->once()
            ->andReturn(['car' => 'park']);
        $request->shouldReceive('getParsedBody')
            ->times(3)
            ->andReturn($object);
        $request->shouldReceive('getHeader')
            ->with('authorization')
            ->once()
            ->andReturn([]);

        $handler = new JwtAuthHandler('jwt', 'secret');

        $method = new ReflectionMethod(JwtAuthHandler::class, 'getToken');
        $method->setAccessible(true);
        $result = $method->invokeArgs($handler, [$request]);

        $this->assertSame($result, 'abc.def.ghi');
    }

    /**
     * @covers PsrJwt\JwtAuth::getToken
     * @uses PsrJwt\JwtAuth::hasJwt
     * @expectedException PsrJwt\JwtAuthException
     * @expectedExceptionMessage JWT Token not set
     */
    public function testGetTokenFromBodyNull()
    {
        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getServerParams')
            ->once()
            ->andReturn(['foo' => 'bar']);
        $request->shouldReceive('getCookieParams')
            ->once()
            ->andReturn(['hello' => 'world']);
        $request->shouldReceive('getQueryParams')
            ->once()
            ->andReturn(['car' => 'park']);
        $request->shouldReceive('getParsedBody')
            ->once()
            ->andReturn(null);
        $request->shouldReceive('getHeader')
            ->with('authorization')
            ->once()
            ->andReturn([]);

        $handler = new JwtAuthHandler('jwt', 'secret');

        $method = new ReflectionMethod(JwtAuthHandler::class, 'getToken');
        $method->setAccessible(true);
        $method->invokeArgs($handler, [$request]);
    }

    public function testGetTokenFromBearer()
    {
        $object = new stdClass();
        $object->jwt = 'abc.def.ghi';

        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getServerParams')
            ->once()
            ->andReturn(['foo' => 'bar']);
        $request->shouldReceive('getCookieParams')
            ->once()
            ->andReturn(['hello' => 'world']);
        $request->shouldReceive('getQueryParams')
            ->once()
            ->andReturn(['car' => 'park']);
        $request->shouldReceive('getParsedBody')
            ->times(3)
            ->andReturn(null);
        $request->shouldReceive('getHeader')
            ->with('authorization')
            ->once()
            ->andReturn(['bearer abc.def.ghi']);

        $handler = new JwtAuthHandler('jwt', 'secret');

        $method = new ReflectionMethod(JwtAuthHandler::class, 'getToken');
        $method->setAccessible(true);
        $result = $method->invokeArgs($handler, [$request]);

        $this->assertSame($result, 'abc.def.ghi');
    }

    /**
     * @covers PsrJwt\JwtAuth::getToken
     * @uses PsrJwt\JwtAuth::hasJwt
     * @uses PsrJwt\JwtAuthException::__construct
     * @expectedException PsrJwt\JwtAuthException
     * @expectedMessage JWT Token not set
     * @expectedExceptionCode 1
     */
    public function testGetTokenNoJwt()
    {
        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getServerParams')
            ->once()
            ->andReturn(['foo' => 'bar']);
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

        $handler = new JwtAuthHandler('jwt', 'secret');

        $method = new ReflectionMethod(JwtAuthHandler::class, 'getToken');
        $method->setAccessible(true);
        $method->invokeArgs($handler, [$request]);
    }

    /**
     * @covers PsrJwt\JwtAuthHandler::getSecret
     */
    public function testGetSecret()
    {
        $handler = new JwtAuthHandler('jwt', 'secret');

        $method = new ReflectionMethod(JwtAuthHandler::class, 'getSecret');
        $method->setAccessible(true);
        $result = $method->invoke($handler);

        $this->assertSame('secret', $result);
    }

    /**
     * @covers PsrJwt\JwtAuthHandler::validate
     */
    public function testValidate()
    {
        $jwt = Jwt::builder();
        $token = $jwt->setSecret('Secret123!456$')
            ->setIssuer('localhost')
            ->build()
            ->getToken();

        $handler = new JwtAuthHandler('jwt', 'Secret123!456$');

        $method = new ReflectionMethod(JwtAuthHandler::class, 'validate');
        $method->setAccessible(true);
        $result = $method->invokeArgs($handler, [$token]);

        $this->assertTrue($result);
    }

    /**
     * @covers PsrJwt\JwtAuth::validate
     * @expectedException PsrJwt\JwtAuthException
     * @expectedExceptionMessage Signature is invalid.
     * @expectedExceptionCode 3
     */
    public function testValidateBadSecret()
    {
        $jwt = Jwt::builder();
        $token = $jwt->setSecret('Secret123!456$')
            ->setIssuer('localhost')
            ->build()
            ->getToken();

        $handler = new JwtAuthHandler('jwt', 'Secret');

        $method = new ReflectionMethod(JwtAuthHandler::class, 'validate');
        $method->setAccessible(true);
        $method->invokeArgs($handler, [$token]);
    }

    /**
     * @covers PsrJwt\JwtAuth::validate
     * @expectedException PsrJwt\JwtAuthException
     * @expectedExceptionMessage Expiration claim has expired.
     * @expectedExceptionCode 4
     */
    public function testValidateBadExpiration()
    {
        $jwt = Jwt::builder();
        $token = $jwt->setSecret('Secret123!456$')
            ->setIssuer('localhost')
            ->setPayloadClaim('exp', time() - 10)
            ->build()
            ->getToken();

        $handler = new JwtAuthHandler('jwt', 'Secret123!456$');

        $method = new ReflectionMethod(JwtAuthHandler::class, 'validate');
        $method->setAccessible(true);
        $method->invokeArgs($handler, [$token]);
    }

    /**
     * @covers PsrJwt\JwtAuth::validate
     * @expectedException PsrJwt\JwtAuthException
     * @expectedExceptionMessage Not Before claim has not elapsed.
     * @expectedExceptionCode 5
     */
    public function testValidateBadNotBefore()
    {
        $jwt = Jwt::builder();
        $token = $jwt->setSecret('Secret123!456$')
            ->setIssuer('localhost')
            ->setPayloadClaim('nbf', time() + 60)
            ->build()
            ->getToken();

        $handler = new JwtAuthHandler('jwt', 'Secret123!456$');

        $method = new ReflectionMethod(JwtAuthHandler::class, 'validate');
        $method->setAccessible(true);
        $method->invokeArgs($handler, [$token]);
    }
}
