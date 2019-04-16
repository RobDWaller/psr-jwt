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
     * @uses PsrJwt\JwtAuthHandler
     * @uses PsrJwt\JwtFactory
     * @uses PsrJwt\JwtValidate
     * @uses PsrJwt\JwtParse
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
     * @uses PsrJwt\JwtFactory
     * @uses PsrJwt\JwtValidate
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
     * @uses PsrJwt\JwtAuthHandler
     * @uses PsrJwt\JwtFactory
     * @uses PsrJwt\JwtValidate
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
     * @uses PsrJwt\JwtAuthHandler
     * @uses PsrJwt\JwtFactory
     * @uses PsrJwt\JwtValidate
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
     * @uses PsrJwt\JwtAuthHandler
     * @uses PsrJwt\JwtFactory
     * @uses PsrJwt\JwtValidate
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

    public function tearDown() {
        m::close();
    }
}
