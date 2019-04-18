<?php

namespace Test;

use PHPUnit\Framework\TestCase;
use PsrJwt\JwtAuthHandler;
use PsrJwt\Factory\Jwt;
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
     * @uses PsrJwt\JwtAuthHandler
     * @uses PsrJwt\Factory\Jwt
     * @uses PsrJwt\Helper\Validate
     * @uses PsrJwt\Helper\Parse
     * @uses PsrJwt\Parser\Body
     * @uses PsrJwt\Parser\Bearer
     * @uses PsrJwt\Parser\Server
     * @uses PsrJwt\Parser\Query
     * @uses PsrJwt\Parser\Cookie
     */
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
     * @uses PsrJwt\JwtAuthHandler::__construct
     */
    public function testJwtAuthHandlerHasJwt()
    {
        $handler = new JwtAuthHandler('jwt', 'secret');

        $method = new ReflectionMethod(JwtAuthHandler::class, 'hasJwt');
        $method->setAccessible(true);
        $result = $method->invokeArgs($handler, ['abc.abc.abc']);

        $this->assertTrue($result);
    }

    /**
     * @covers PsrJwt\JwtAuthHandler::hasJwt
     * @uses PsrJwt\JwtAuthHandler::__construct
     */
    public function testJwtAuthHandlerHasJwtEmpty()
    {
        $handler = new JwtAuthHandler('jwt', 'secret');

        $method = new ReflectionMethod(JwtAuthHandler::class, 'hasJwt');
        $method->setAccessible(true);
        $result = $method->invokeArgs($handler, ['']);

        $this->assertFalse($result);
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
     * @uses PsrJwt\JwtAuthHandler
     * @uses PsrJwt\Factory\Jwt
     * @uses PsrJwt\Helper\Validate
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

        $this->assertSame(200, $result->getStatusCode());
        $this->assertSame('Ok', $result->getReasonPhrase());
    }

    /**
     * @covers PsrJwt\JwtAuthHandler::validate
     * @uses PsrJwt\JwtAuthHandler
     * @uses PsrJwt\Factory\Jwt
     * @uses PsrJwt\Helper\Validate
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
        $result = $method->invokeArgs($handler, [$token]);

        $this->assertSame(401, $result->getStatusCode());
        $this->assertSame('Unauthorized: Signature is invalid.', $result->getReasonPhrase());
    }

    /**
     * @covers PsrJwt\JwtAuthHandler::validate
     * @uses PsrJwt\JwtAuthHandler
     * @uses PsrJwt\Factory\Jwt
     * @uses PsrJwt\Helper\Validate
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
        $result = $method->invokeArgs($handler, [$token]);

        $this->assertSame(401, $result->getStatusCode());
        $this->assertSame('Unauthorized: Expiration claim has expired.', $result->getReasonPhrase());
    }

    /**
     * @covers PsrJwt\JwtAuthHandler::validate
     * @uses PsrJwt\JwtAuthHandler
     * @uses PsrJwt\Factory\Jwt
     * @uses PsrJwt\Helper\Validate
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
        $result = $method->invokeArgs($handler, [$token]);

        $this->assertSame(401, $result->getStatusCode());
        $this->assertSame('Unauthorized: Not Before claim has not elapsed.', $result->getReasonPhrase());
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

    /**
     * @covers PsrJwt\JwtAuthHandler::getToken
     * @uses PsrJwt\JwtAuthHandler
     * @uses PsrJwt\Helper\Parse
     * @uses PsrJwt\Parser\Bearer
     */
    public function testGetToken()
    {
        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getHeader')
            ->with('authorization')
            ->once()
            ->andReturn(['Bearer abc.def.ghi']);

        $handler = new JwtAuthHandler('jwt', 'secret');

        $method = new ReflectionMethod(JwtAuthHandler::class, 'getToken');
        $method->setAccessible(true);
        $result = $method->invokeArgs($handler, [$request]);

        $this->assertSame('abc.def.ghi', $result);
    }

    /**
     * @expectedException ReallySimpleJWT\Exception\ValidateException
     * @expectedExceptionMessage JSON Web Token not set.
     * @expectedExceptionCode 11
     * @covers PsrJwt\JwtAuthHandler::getToken
     * @uses PsrJwt\JwtAuthHandler
     * @uses PsrJwt\Helper\Parse
     * @uses PsrJwt\Parser\Bearer
     * @uses PsrJwt\Parser\Server
     * @uses PsrJwt\Parser\Body
     * @uses PsrJwt\Parser\Cookie
     * @uses PsrJwt\Parser\Query
     */
    public function testGetTokenNoToken()
    {
        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getHeader')
            ->with('authorization')
            ->once()
            ->andReturn([]);
        $request->shouldReceive('getServerParams')
            ->once()
            ->andReturn([]);
        $request->shouldReceive('getCookieParams')
            ->once()
            ->andReturn([]);
        $request->shouldReceive('getQueryParams')
            ->once()
            ->andReturn([]);
        $request->shouldReceive('getParsedBody')
            ->twice()
            ->andReturn([]);

        $handler = new JwtAuthHandler('jwt', 'secret');

        $method = new ReflectionMethod(JwtAuthHandler::class, 'getToken');
        $method->setAccessible(true);
        $result = $method->invokeArgs($handler, [$request]);
    }

    public function tearDown() {
        m::close();
    }
}
