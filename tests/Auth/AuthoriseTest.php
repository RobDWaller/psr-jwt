<?php

namespace Tests\Auth;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use PsrJwt\Auth\Authorise;
use PsrJwt\Auth\Auth;
use PsrJwt\Factory\Jwt;
use ReflectionMethod;
use Mockery as m;

class AuthoriseTest extends TestCase
{
    /**
     * @covers PsrJwt\Auth\Authorise::__construct
     */
    public function testAuthorise()
    {
        $auth = new Authorise('secret', 'jwt');
        $this->assertInstanceOf(Authorise::class, $auth);
    }

    /**
     * @covers PsrJwt\Auth\Authorise::authorise
     * @uses PsrJwt\Auth\Authorise
     * @uses PsrJwt\Auth\Auth
     * @uses PsrJwt\Factory\Jwt
     * @uses PsrJwt\Validation\Validate
     * @uses PsrJwt\Parser\Parse
     * @uses PsrJwt\Parser\Body
     * @uses PsrJwt\Parser\Bearer
     * @uses PsrJwt\Parser\Query
     * @uses PsrJwt\Parser\Cookie
     * @uses PsrJwt\Parser\Request
     */
    public function testAuthoriseOk()
    {
        $jwt = new Jwt();
        $jwt = $jwt->builder();
        $token = $jwt->setSecret('Secret123!456$')
            ->setIssuer('localhost')
            ->build()
            ->getToken();

        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getCookieParams')
            ->once()
            ->andReturn(['foo' => 'bar']);
        $request->shouldReceive('getQueryParams')
            ->once()
            ->andReturn(['jwt' => $token]);
        $request->shouldReceive('getParsedBody')
            ->twice()
            ->andReturn([]);
        $request->shouldReceive('getHeader')
            ->with('authorization')
            ->once()
            ->andReturn([]);

        $authorise = new Authorise('Secret123!456$', 'jwt');

        $result = $authorise->authorise($request);

        $this->assertInstanceOf(Auth::class, $result);
        $this->assertSame(200, $result->getCode());
        $this->assertSame('Ok', $result->getMessage());
    }

    /**
     * @covers PsrJwt\Auth\Authorise::authorise
     * @uses PsrJwt\Auth\Authorise
     * @uses PsrJwt\Auth\Auth
     * @uses PsrJwt\Factory\Jwt
     * @uses PsrJwt\Validation\Validate
     * @uses PsrJwt\Parser\Parse
     * @uses PsrJwt\Parser\Body
     * @uses PsrJwt\Parser\Bearer
     * @uses PsrJwt\Parser\Query
     * @uses PsrJwt\Parser\Cookie
     * @uses PsrJwt\Parser\Request
     * @uses PsrJwt\Parser\ParseException
     */
    public function testAuthoriseBadRequest()
    {
        $jwt = new Jwt();
        $jwt = $jwt->builder();

        $request = m::mock(ServerRequestInterface::class);
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

        $auth = new Authorise('Secret123!456$', 'jwt');

        $result = $auth->authorise($request);

        $this->assertSame(400, $result->getCode());
        $this->assertSame('Bad Request: JSON Web Token not set in request.', $result->getMessage());
    }

    /**
     * @covers PsrJwt\Auth\Authorise::validate
     * @uses PsrJwt\Auth\Authorise
     * @uses PsrJwt\Auth\Auth
     * @uses PsrJwt\Factory\Jwt
     * @uses PsrJwt\Validation\Validate
     */
    public function testValidate()
    {
        $jwt = new Jwt();
        $jwt = $jwt->builder();
        $token = $jwt->setSecret('Secret123!456$')
            ->setIssuer('localhost')
            ->build()
            ->getToken();

        $auth = new Authorise('Secret123!456$', 'jwt');

        $method = new ReflectionMethod(Authorise::class, 'validate');
        $method->setAccessible(true);
        $result = $method->invokeArgs($auth, [$token]);

        $this->assertSame(200, $result->getCode());
        $this->assertSame('Ok', $result->getMessage());
    }

    /**
     * @covers PsrJwt\Auth\Authorise::validate
     * @uses PsrJwt\Auth\Authorise
     * @uses PsrJwt\Auth\Auth
     * @uses PsrJwt\Factory\Jwt
     * @uses PsrJwt\Validation\Validate
     */
    public function testValidateBadSecret()
    {
        $jwt = new Jwt();
        $jwt = $jwt->builder();
        $token = $jwt->setSecret('Secret123!456$')
            ->setIssuer('localhost')
            ->build()
            ->getToken();

        $auth = new Authorise('Secret', 'jwt');

        $method = new ReflectionMethod(Authorise::class, 'validate');
        $method->setAccessible(true);
        $result = $method->invokeArgs($auth, [$token]);

        $this->assertSame(401, $result->getCode());
        $this->assertSame('Unauthorized: Signature is invalid.', $result->getMessage());
    }

    /**
     * @covers PsrJwt\Auth\Authorise::validate
     * @uses PsrJwt\Auth\Authorise
     * @uses PsrJwt\Auth\Auth
     * @uses PsrJwt\Factory\Jwt
     * @uses PsrJwt\Validation\Validate
     */
    public function testValidateBadExpiration()
    {
        $jwt = new Jwt();
        $jwt = $jwt->builder();
        $token = $jwt->setSecret('Secret123!456$')
            ->setIssuer('localhost')
            ->setPayloadClaim('exp', time() - 10)
            ->build()
            ->getToken();

        $auth = new Authorise('Secret123!456$', 'jwt');

        $method = new ReflectionMethod(Authorise::class, 'validate');
        $method->setAccessible(true);
        $result = $method->invokeArgs($auth, [$token]);

        $this->assertSame(401, $result->getCode());
        $this->assertSame('Unauthorized: Expiration claim has expired.', $result->getMessage());
    }

    /**
     * @covers PsrJwt\Auth\Authorise::validate
     * @uses PsrJwt\Auth\Authorise
     * @uses PsrJwt\Auth\Auth
     * @uses PsrJwt\Factory\Jwt
     * @uses PsrJwt\Validation\Validate
     */
    public function testValidateBadNotBefore()
    {
        $jwt = new Jwt();
        $jwt = $jwt->builder();
        $token = $jwt->setSecret('Secret123!456$')
            ->setIssuer('localhost')
            ->setPayloadClaim('nbf', time() + 60)
            ->build()
            ->getToken();

        $auth = new Authorise('Secret123!456$', 'jwt');

        $method = new ReflectionMethod(Authorise::class, 'validate');
        $method->setAccessible(true);
        $result = $method->invokeArgs($auth, [$token]);

        $this->assertSame(401, $result->getCode());
        $this->assertSame('Unauthorized: Not Before claim has not elapsed.', $result->getMessage());
    }

    /**
     * @covers PsrJwt\Auth\Authorise::validationResponse
     * @uses PsrJwt\Auth\Authorise::__construct
     * @uses PsrJwt\Auth\Auth
     */
    public function testValidationResponse()
    {
        $auth = new Authorise('secret', 'jwt');

        $method = new ReflectionMethod(Authorise::class, 'validationResponse');
        $method->setAccessible(true);
        $result = $method->invokeArgs($auth, [0, 'Ok']);

        $this->assertInstanceOf(Auth::class, $result);
        $this->assertSame(200, $result->getCode());
        $this->assertSame('Ok', $result->getMessage());
    }

    /**
     * @covers PsrJwt\Auth\Authorise::validationResponse
     * @uses PsrJwt\Auth\Authorise::__construct
     * @uses PsrJwt\Auth\Auth
     */
    public function testValidationResponseErrors()
    {
        $auth = new Authorise('secret', 'jwt');

        $method = new ReflectionMethod(Authorise::class, 'validationResponse');
        $method->setAccessible(true);

        $errors = [
            [1, 'Error 1'],
            [2, 'Error 2'],
            [3, 'Error 3'],
            [4, 'Error 4'],
            [5, 'Error 5']
        ];

        foreach ($errors as $error) {
            $result = $method->invokeArgs($auth, [$error[0], $error[1]]);

            $this->assertInstanceOf(Auth::class, $result);
            $this->assertSame(401, $result->getCode());
            $this->assertSame('Unauthorized: ' . $error[1], $result->getMessage());
        }
    }

    /**
     * @covers PsrJwt\Auth\Authorise::getToken
     * @uses PsrJwt\Auth\Authorise
     * @uses PsrJwt\Parser\Parse
     * @uses PsrJwt\Parser\Bearer
     * @uses PsrJwt\Parser\Cookie
     * @uses PsrJwt\Parser\Query
     * @uses PsrJwt\Parser\Body
     * @uses PsrJwt\Parser\Request
     */
    public function testGetToken()
    {
        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getHeader')
            ->with('authorization')
            ->once()
            ->andReturn(['Bearer abc.def.ghi']);

        $auth = new Authorise('secret', 'jwt');

        $method = new ReflectionMethod(Authorise::class, 'getToken');
        $method->setAccessible(true);
        $result = $method->invokeArgs($auth, [$request]);

        $this->assertSame('abc.def.ghi', $result);
    }

    /**
     * @covers PsrJwt\Auth\Authorise::getToken
     * @uses PsrJwt\Auth\Authorise
     * @uses PsrJwt\Parser\Parse
     * @uses PsrJwt\Parser\Bearer
     * @uses PsrJwt\Parser\Cookie
     * @uses PsrJwt\Parser\Body
     * @uses PsrJwt\Parser\Query
     * @uses PsrJwt\Parser\Request
     */
    public function testGetTokenCookie()
    {
        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getHeader')
            ->with('authorization')
            ->once()
            ->andReturn([]);
        $request->shouldReceive('getCookieParams')
            ->once()
            ->andReturn(['token' => 'abc.123.def']);

        $auth = new Authorise('secret', 'token');

        $method = new ReflectionMethod(Authorise::class, 'getToken');
        $method->setAccessible(true);
        $result = $method->invokeArgs($auth, [$request]);

        $this->assertSame('abc.123.def', $result);
    }

    /**
     * @covers PsrJwt\Auth\Authorise::getToken
     * @uses PsrJwt\Auth\Authorise
     * @uses PsrJwt\Parser\Parse
     * @uses PsrJwt\Parser\Bearer
     * @uses PsrJwt\Parser\Cookie
     * @uses PsrJwt\Parser\Body
     * @uses PsrJwt\Parser\Query
     * @uses PsrJwt\Parser\Request
     */
    public function testGetTokenBody()
    {
        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getHeader')
            ->with('authorization')
            ->once()
            ->andReturn([]);
        $request->shouldReceive('getCookieParams')
            ->once()
            ->andReturn([]);
        $request->shouldReceive('getParsedBody')
            ->once()
            ->andReturn(['json_token_1' => '123.abc.def']);

        $auth = new Authorise('secret', 'json_token_1');

        $method = new ReflectionMethod(Authorise::class, 'getToken');
        $method->setAccessible(true);
        $result = $method->invokeArgs($auth, [$request]);

        $this->assertSame('123.abc.def', $result);
    }

    /**
     * @covers PsrJwt\Auth\Authorise::getToken
     * @uses PsrJwt\Auth\Authorise
     * @uses PsrJwt\Parser\Parse
     * @uses PsrJwt\Parser\Bearer
     * @uses PsrJwt\Parser\Cookie
     * @uses PsrJwt\Parser\Body
     * @uses PsrJwt\Parser\Query
     * @uses PsrJwt\Parser\Request
     */
    public function testGetTokenBodyObject()
    {
        $token = new \stdClass();
        $token->my_token = 'ghi.123.xyz';

        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getHeader')
            ->with('authorization')
            ->once()
            ->andReturn([]);
        $request->shouldReceive('getCookieParams')
            ->once()
            ->andReturn([]);
        $request->shouldReceive('getParsedBody')
            ->twice()
            ->andReturn($token);

        $auth = new Authorise('secret', 'my_token');

        $method = new ReflectionMethod(Authorise::class, 'getToken');
        $method->setAccessible(true);
        $result = $method->invokeArgs($auth, [$request]);

        $this->assertSame('ghi.123.xyz', $result);
    }

    /**
     * @covers PsrJwt\Auth\Authorise::getToken
     * @uses PsrJwt\Auth\Authorise
     * @uses PsrJwt\Parser\Parse
     * @uses PsrJwt\Parser\Bearer
     * @uses PsrJwt\Parser\Cookie
     * @uses PsrJwt\Parser\Body
     * @uses PsrJwt\Parser\Query
     * @uses PsrJwt\Parser\Request
     */
    public function testGetTokenQuery()
    {
        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getHeader')
            ->with('authorization')
            ->once()
            ->andReturn([]);
        $request->shouldReceive('getCookieParams')
            ->once()
            ->andReturn([]);
        $request->shouldReceive('getParsedBody')
            ->twice()
            ->andReturn([]);
        $request->shouldReceive('getQueryParams')
            ->once()
            ->andReturn(['auth_token' => '456.gfv.3-1']);

        $auth = new Authorise('secret', 'auth_token');

        $method = new ReflectionMethod(Authorise::class, 'getToken');
        $method->setAccessible(true);
        $result = $method->invokeArgs($auth, [$request]);

        $this->assertSame('456.gfv.3-1', $result);
    }

    /**
     * @expectedException PsrJwt\Parser\ParseException
     * @expectedExceptionMessage JSON Web Token not set in request.
     * @covers PsrJwt\Auth\Authorise::getToken
     * @uses PsrJwt\Auth\Authorise
     * @uses PsrJwt\Parser\Parse
     * @uses PsrJwt\Parser\Bearer
     * @uses PsrJwt\Parser\Body
     * @uses PsrJwt\Parser\Cookie
     * @uses PsrJwt\Parser\Query
     * @uses PsrJwt\Parser\Request
     * @uses PsrJwt\Parser\ParseException
     */
    public function testGetTokenNoToken()
    {
        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getHeader')
            ->with('authorization')
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

        $auth = new Authorise('secret', 'jwt');

        $method = new ReflectionMethod(Authorise::class, 'getToken');
        $method->setAccessible(true);
        $result = $method->invokeArgs($auth, [$request]);
    }

    public function tearDown()
    {
        m::close();
    }
}
