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
    /**
     * @covers PsrJwt\JwtAuthHandler::__construct
     */
    public function testJwtAuthHandler()
    {
        $handler = new JwtAuthHandler('jwt', 'secret');

        $this->assertInstanceOf(JwtAuthHandler::class, $handler);
        $this->assertInstanceOf(RequestHandlerInterface::class, $handler);
    }

    /**
     * @covers PsrJwt\JwtAuthHandler::handle
     * @uses PsrJwt\JwtAuthHandler::__construct
     * @uses PsrJwt\JwtAuthHandler::getBearerToken
     * @uses PsrJwt\JwtAuthHandler::getSecret
     * @uses PsrJwt\JwtAuthHandler::getToken
     * @uses PsrJwt\JwtAuthHandler::hasJwt
     * @uses PsrJwt\JwtAuthHandler::parseBearerToken
     * @uses PsrJwt\JwtAuthHandler::parseRequestBody
     * @uses PsrJwt\JwtAuthHandler::validate
     * @uses PsrJwt\JwtFactory::builder
     * @uses PsrJwt\JwtFactory::parser
     * @uses PsrJwt\JwtAuthHandler::validationResponse
     * @uses PsrJwt\JwtValidate::__construct
     * @uses PsrJwt\JwtValidate::validate
     * @uses PsrJwt\JwtValidate::validateNotBefore
     */
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
     * @uses PsrJwt\JwtAuthHandler::__construct
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
     * @uses PsrJwt\JwtAuthHandler::__construct
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
     * @uses PsrJwt\JwtAuthHandler::__construct
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
     * @uses PsrJwt\JwtAuthHandler::__construct
     * @uses PsrJwt\JwtAuthHandler::parseRequestBody
     * @uses PsrJwt\JwtAuthHandler::parseBearerToken
     * @uses PsrJwt\JwtAuthHandler::getBearerToken
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
     * @covers PsrJwt\JwtAuthHandler::getToken
     * @uses PsrJwt\JwtAuthHandler::hasJwt
     * @uses PsrJwt\JwtAuthHandler::__construct
     * @uses PsrJwt\JwtAuthHandler::parseRequestBody
     * @uses PsrJwt\JwtAuthHandler::parseBearerToken
     * @uses PsrJwt\JwtAuthHandler::getBearerToken
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
     * @covers PsrJwt\JwtAuthHandler::getToken
     * @uses PsrJwt\JwtAuthHandler::hasJwt
     * @uses PsrJwt\JwtAuthHandler::__construct
     * @uses PsrJwt\JwtAuthHandler::parseRequestBody
     * @uses PsrJwt\JwtAuthHandler::parseBearerToken
     * @uses PsrJwt\JwtAuthHandler::getBearerToken
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
     * @covers PsrJwt\JwtAuthHandler::getToken
     * @uses PsrJwt\JwtAuthHandler::hasJwt
     * @uses PsrJwt\JwtAuthHandler::__construct
     * @uses PsrJwt\JwtAuthHandler::parseRequestBody
     * @uses PsrJwt\JwtAuthHandler::parseBearerToken
     * @uses PsrJwt\JwtAuthHandler::getBearerToken
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
     * @covers PsrJwt\JwtAuthHandler::getToken
     * @uses PsrJwt\JwtAuthHandler::hasJwt
     * @uses PsrJwt\JwtAuthHandler::__construct
     * @uses PsrJwt\JwtAuthHandler::parseRequestBody
     * @uses PsrJwt\JwtAuthHandler::parseBearerToken
     * @uses PsrJwt\JwtAuthHandler::getBearerToken
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
     * @uses PsrJwt\JwtAuthHandler::__construct
     * @uses PsrJwt\JwtAuthHandler::parseRequestBody
     * @uses PsrJwt\JwtAuthHandler::parseBearerToken
     * @uses PsrJwt\JwtAuthHandler::getBearerToken
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

    /**
     * @covers PsrJwt\JwtAuthHandler::getToken
     * @uses PsrJwt\JwtAuthHandler::hasJwt
     * @uses PsrJwt\JwtAuthHandler::__construct
     * @uses PsrJwt\JwtAuthHandler::parseRequestBody
     * @uses PsrJwt\JwtAuthHandler::parseBearerToken
     * @uses PsrJwt\JwtAuthHandler::getBearerToken
     */
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
     * @covers PsrJwt\JwtAuthHandler::getToken
     * @uses PsrJwt\JwtAuthHandler::hasJwt
     * @uses PsrJwt\JwtAuthHandler::__construct
     * @uses PsrJwt\JwtAuthHandler::parseRequestBody
     * @uses PsrJwt\JwtAuthHandler::parseBearerToken
     * @uses PsrJwt\JwtAuthHandler::getBearerToken
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
     * @uses PsrJwt\JwtAuthHandler::__construct
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
     * @uses PsrJwt\JwtAuthHandler::__construct
     * @uses PsrJwt\JwtAuthHandler::getSecret
     * @uses PsrJwt\JwtFactory::builder
     * @uses PsrJwt\JwtFactory::parser
     * @uses PsrJwt\JwtAuthHandler::validationResponse
     * @uses PsrJwt\JwtValidate::__construct
     * @uses PsrJwt\JwtValidate::validate
     * @uses PsrJwt\JwtValidate::validateNotBefore
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
     * @covers PsrJwt\JwtAuthHandler::validate
     * @uses PsrJwt\JwtAuthHandler::__construct
     * @uses PsrJwt\JwtAuthHandler::getSecret
     * @uses PsrJwt\JwtFactory::builder
     * @uses PsrJwt\JwtFactory::parser
     * @uses PsrJwt\JwtAuthHandler::validationResponse
     * @uses PsrJwt\JwtValidate::__construct
     * @uses PsrJwt\JwtValidate::validate
     * @uses PsrJwt\JwtValidate::validateNotBefore
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
     * @covers PsrJwt\JwtAuthHandler::validate
     * @uses PsrJwt\JwtAuthHandler::__construct
     * @uses PsrJwt\JwtAuthHandler::getSecret
     * @uses PsrJwt\JwtFactory::builder
     * @uses PsrJwt\JwtFactory::parser
     * @uses PsrJwt\JwtAuthHandler::validationResponse
     * @uses PsrJwt\JwtValidate::__construct
     * @uses PsrJwt\JwtValidate::validate
     * @uses PsrJwt\JwtValidate::validateNotBefore
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
     * @covers PsrJwt\JwtAuthHandler::validate
     * @uses PsrJwt\JwtAuthHandler::__construct
     * @uses PsrJwt\JwtAuthHandler::getSecret
     * @uses PsrJwt\JwtFactory::builder
     * @uses PsrJwt\JwtFactory::parser
     * @uses PsrJwt\JwtValidate::__construct
     * @uses PsrJwt\JwtValidate::validate
     * @uses PsrJwt\JwtValidate::validateNotBefore
     * @uses PsrJwt\JwtAuthHandler::validationResponse
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

    /**
     * @covers PsrJwt\JwtAuthHandler::parseRequestBody
     * @uses PsrJwt\JwtAuthHandler::__construct
     */
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

    /**
     * @covers PsrJwt\JwtAuthHandler::parseRequestBody
     * @uses PsrJwt\JwtAuthHandler::__construct
     */
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

    /**
     * @covers PsrJwt\JwtAuthHandler::parseRequestBody
     * @uses PsrJwt\JwtAuthHandler::__construct
     */
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

    /**
     * @covers PsrJwt\JwtAuthHandler::parseRequestBody
     * @uses PsrJwt\JwtAuthHandler::__construct
     */
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

    /**
     * @covers PsrJwt\JwtAuthHandler::getBearerToken
     * @uses PsrJwt\JwtAuthHandler::__construct
     */
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

    /**
     * @covers PsrJwt\JwtAuthHandler::getBearerToken
     * @uses PsrJwt\JwtAuthHandler::__construct
     */
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

    /**
     * @covers PsrJwt\JwtAuthHandler::getBearerToken
     * @uses PsrJwt\JwtAuthHandler::__construct
     */
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

    /**
     * @covers PsrJwt\JwtAuthHandler::parseBearerToken
     * @uses PsrJwt\JwtAuthHandler::__construct
     * @uses PsrJwt\JwtAuthHandler::getBearerToken
     */
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

    /**
     * @covers PsrJwt\JwtAuthHandler::parseBearerToken
     * @uses PsrJwt\JwtAuthHandler::__construct
     * @uses PsrJwt\JwtAuthHandler::getBearerToken
     */
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

    /**
     * @covers PsrJwt\JwtAuthHandler::validationResponse
     * @uses PsrJwt\JwtAuthHandler::__construct
     */
    public function testValidationResponse()
    {
        $handler = new JwtAuthHandler('jwt', 'secret');

        $method = new ReflectionMethod(JwtAuthHandler::class, 'validationResponse');
        $method->setAccessible(true);
        $result = $method->invokeArgs($handler, [0, 'Ok']);

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertSame(200, $result->getStatusCode());
        $this->assertSame('Ok', $result->getReasonPhrase());
    }

    /**
     * @covers PsrJwt\JwtAuthHandler::validationResponse
     * @uses PsrJwt\JwtAuthHandler::__construct
     */
    public function testValidationResponseErrors()
    {
        $handler = new JwtAuthHandler('jwt', 'secret');

        $method = new ReflectionMethod(JwtAuthHandler::class, 'validationResponse');
        $method->setAccessible(true);

        $errors = [
            [1, 'Error 1'],
            [2, 'Error 1'],
            [3, 'Error 1'],
            [4, 'Error 1'],
            [5, 'Error 1']
        ];

        foreach ($errors as $error) {
            $result = $method->invokeArgs($handler, [$error[0], $error[1]]);

            $this->assertInstanceOf(ResponseInterface::class, $result);
            $this->assertSame(401, $result->getStatusCode());
            $this->assertSame('Unauthorized: ' . $error[1], $result->getReasonPhrase());
        }
    }
}
