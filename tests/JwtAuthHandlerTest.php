<?php

namespace Test;

use PHPUnit\Framework\TestCase;
use PsrJwt\JwtAuthHandler;
use PsrJwt\JwtFactory;
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
        $jwt = JwtFactory::builder();
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
     * @covers PsrJwt\JwtAuthHandler::getToken
     * @uses PsrJwt\JwtAuthHandler::hasJwt
     * @expectedException ReallySimpleJWT\Exception\ValidateException
     * @expectedExceptionCode 11
     * @expectedExceptionMessage JSON Web Token not set.
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
     * @uses PsrJwt\JwtAuthHandler::hasJwt
     * @expectedException ReallySimpleJWT\Exception\ValidateException
     * @expectedExceptionCode 11
     * @expectedExceptionMessage JSON Web Token not set.
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
        $jwt = JwtFactory::builder();
        $token = $jwt->setSecret('Secret123!456$')
            ->setIssuer('localhost')
            ->build()
            ->getToken();

        $handler = new JwtAuthHandler('jwt', 'Secret123!456$');

        $method = new ReflectionMethod(JwtAuthHandler::class, 'validate');
        $method->setAccessible(true);
        $result = $method->invokeArgs($handler, [$token]);

        $this->assertSame(200, $result->getStatusCode());
        $this->assertSame('Ok', $result->getReasonPhrase());
    }

    /**
     * @covers PsrJwt\JwtAuth::validate
     */
    public function testValidateBadSecret()
    {
        $jwt = JwtFactory::builder();
        $token = $jwt->setSecret('Secret123!456$')
            ->setIssuer('localhost')
            ->build()
            ->getToken();

        $handler = new JwtAuthHandler('jwt', 'Secret');

        $method = new ReflectionMethod(JwtAuthHandler::class, 'validate');
        $method->setAccessible(true);
        $result = $method->invokeArgs($handler, [$token]);

        $this->assertSame(401, $result->getStatusCode());
        $this->assertSame('Unauthorized: Signature is invalid.', $result->getReasonPhrase());
    }

    /**
     * @covers PsrJwt\JwtAuth::validate
     */
    public function testValidateBadExpiration()
    {
        $jwt = JwtFactory::builder();
        $token = $jwt->setSecret('Secret123!456$')
            ->setIssuer('localhost')
            ->setPayloadClaim('exp', time() - 10)
            ->build()
            ->getToken();

        $handler = new JwtAuthHandler('jwt', 'Secret123!456$');

        $method = new ReflectionMethod(JwtAuthHandler::class, 'validate');
        $method->setAccessible(true);
        $result = $method->invokeArgs($handler, [$token]);

        $this->assertSame(401, $result->getStatusCode());
        $this->assertSame('Unauthorized: Expiration claim has expired.', $result->getReasonPhrase());
    }

    /**
     * @covers PsrJwt\JwtAuth::validate
     */
    public function testValidateBadNotBefore()
    {
        $jwt = JwtFactory::builder();
        $token = $jwt->setSecret('Secret123!456$')
            ->setIssuer('localhost')
            ->setPayloadClaim('nbf', time() + 60)
            ->build()
            ->getToken();

        $handler = new JwtAuthHandler('jwt', 'Secret123!456$');

        $method = new ReflectionMethod(JwtAuthHandler::class, 'validate');
        $method->setAccessible(true);
        $result = $method->invokeArgs($handler, [$token]);

        $this->assertSame(401, $result->getStatusCode());
        $this->assertSame('Unauthorized: Not Before claim has not elapsed.', $result->getReasonPhrase());
    }

    public function testParseRequestBody()
    {
        $handler = new JwtAuthHandler('jwt', 'secret');

        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getParsedBody')
            ->times(3)
            ->andReturn(['jwt' => 'abc.abc.abc']);

        $method = new ReflectionMethod(JwtAuthHandler::class, 'parseRequestBody');
        $method->setAccessible(true);
        $result = $method->invokeArgs($handler, [$request]);

        $this->assertCount(1, $result);
        $this->assertSame('abc.abc.abc', $result['jwt']);
    }

    public function testParseRequestBodyNull()
    {
        $handler = new JwtAuthHandler('jwt', 'secret');

        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getParsedBody')
            ->times(1)
            ->andReturn(null);

        $method = new ReflectionMethod(JwtAuthHandler::class, 'parseRequestBody');
        $method->setAccessible(true);
        $result = $method->invokeArgs($handler, [$request]);

        $this->assertCount(0, $result);
    }

    public function testParseRequestBodyObject()
    {
        $handler = new JwtAuthHandler('jwt', 'secret');

        $object = new stdClass();
        $object->jwt = 'abc.def.ghi';

        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getParsedBody')
            ->times(3)
            ->andReturn($object);

        $method = new ReflectionMethod(JwtAuthHandler::class, 'parseRequestBody');
        $method->setAccessible(true);
        $result = $method->invokeArgs($handler, [$request]);

        $this->assertCount(1, $result);
        $this->assertSame('abc.def.ghi', $result['jwt']);
    }

    public function testParseRequestBodyObjectNoKey()
    {
        $handler = new JwtAuthHandler('jwt', 'secret');

        $object = new stdClass();

        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getParsedBody')
            ->times(3)
            ->andReturn($object);

        $method = new ReflectionMethod(JwtAuthHandler::class, 'parseRequestBody');
        $method->setAccessible(true);
        $result = $method->invokeArgs($handler, [$request]);

        $this->assertCount(0, $result);
    }

    public function testGetBearerToken()
    {
        $handler = new JwtAuthHandler('jwt', 'secret');

        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getHeader')
            ->with('authorization')
            ->andReturn(['bearer abc.def.ghi']);

        $method = new ReflectionMethod(JwtAuthHandler::class, 'getBearerToken');
        $method->setAccessible(true);
        $result = $method->invokeArgs($handler, [$request]);

        $this->assertSame('abc.def.ghi', $result);
    }

    public function testGetBearerTokenNoBearer()
    {
        $handler = new JwtAuthHandler('jwt', 'secret');

        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getHeader')
            ->with('authorization')
            ->andReturn(['foo', 'bar']);

        $method = new ReflectionMethod(JwtAuthHandler::class, 'getBearerToken');
        $method->setAccessible(true);
        $result = $method->invokeArgs($handler, [$request]);

        $this->assertEmpty($result);
    }

    public function testGetBearerTokenNoAuthorization()
    {
        $handler = new JwtAuthHandler('jwt', 'secret');

        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getHeader')
            ->with('authorization')
            ->andReturn([]);

        $method = new ReflectionMethod(JwtAuthHandler::class, 'getBearerToken');
        $method->setAccessible(true);
        $result = $method->invokeArgs($handler, [$request]);

        $this->assertEmpty($result);
    }

    public function testParseBearerToken()
    {
        $handler = new JwtAuthHandler('jwt', 'secret');

        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getHeader')
            ->with('authorization')
            ->andReturn(['bearer abc.def.ghi']);

        $method = new ReflectionMethod(JwtAuthHandler::class, 'parseBearerToken');
        $method->setAccessible(true);
        $result = $method->invokeArgs($handler, [$request]);

        $this->assertSame('abc.def.ghi', $result['jwt']);
    }

    public function testParseBearerTokenNoBearer()
    {
        $handler = new JwtAuthHandler('jwt', 'secret');

        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getHeader')
            ->with('authorization')
            ->andReturn(['foo', 'bar']);

        $method = new ReflectionMethod(JwtAuthHandler::class, 'parseBearerToken');
        $method->setAccessible(true);
        $result = $method->invokeArgs($handler, [$request]);

        $this->assertEmpty($result);
    }
}
