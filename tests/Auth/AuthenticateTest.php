<?php

namespace Tests\Auth;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use PsrJwt\Auth\Authenticate;
use PsrJwt\Auth\Auth;
use PsrJwt\Factory\Jwt;
use ReflectionMethod;
use Mockery as m;

class AuthenticateTest extends TestCase
{
    /**
     * @covers PsrJwt\Auth\Authenticate::__construct
     */
    public function testAuthenticate()
    {
        $auth = new Authenticate('secret', 'jwt');
        $this->assertInstanceOf(Authenticate::class, $auth);
    }

    /**
     * @covers PsrJwt\Auth\Authenticate::authenticate
     * @uses PsrJwt\Auth\Authenticate
     * @uses PsrJwt\Auth\Auth
     * @uses PsrJwt\Factory\Jwt
     * @uses PsrJwt\Validation\Validate
     * @uses PsrJwt\Parser\Parse
     * @uses PsrJwt\Parser\Body
     * @uses PsrJwt\Parser\Bearer
     * @uses PsrJwt\Parser\Query
     * @uses PsrJwt\Parser\Cookie
     */
    public function testAuthenticateOk()
    {
        $jwt = Jwt::builder();
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

        $authenticate = new Authenticate('Secret123!456$', 'jwt');

        $result = $authenticate->authenticate($request);

        $this->assertInstanceOf(Auth::class, $result);
        $this->assertSame(200, $result->getCode());
        $this->assertSame('Ok', $result->getMessage());
    }

    /**
     * @covers PsrJwt\Auth\Authenticate::authenticate
     * @uses PsrJwt\Auth\Authenticate
     * @uses PsrJwt\Auth\Auth
     * @uses PsrJwt\Factory\Jwt
     * @uses PsrJwt\Validation\Validate
     * @uses PsrJwt\Parser\Parse
     * @uses PsrJwt\Parser\Body
     * @uses PsrJwt\Parser\Bearer
     * @uses PsrJwt\Parser\Query
     * @uses PsrJwt\Parser\Cookie
     */
    public function testAuthenticateBadRequest()
    {
        $jwt = Jwt::builder();

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

        $auth = new Authenticate('Secret123!456$', 'jwt');

        $result = $auth->authenticate($request);

        $this->assertSame(400, $result->getCode());
        $this->assertSame('Bad Request: JSON Web Token not set.', $result->getMessage());
    }

    /**
     * @covers PsrJwt\Auth\Authenticate::hasJwt
     * @uses PsrJwt\Auth\Authenticate::__construct
     */
    public function testAuthenticateHasJwt()
    {
        $auth = new Authenticate('secret', 'jwt');

        $method = new ReflectionMethod(Authenticate::class, 'hasJwt');
        $method->setAccessible(true);
        $result = $method->invokeArgs($auth, ['abc.abc.abc']);

        $this->assertTrue($result);
    }

    /**
     * @covers PsrJwt\Auth\Authenticate::hasJwt
     * @uses PsrJwt\Auth\Authenticate::__construct
     */
    public function testAuthenticateHasJwtEmpty()
    {
        $auth = new Authenticate('secret', 'jwt');

        $method = new ReflectionMethod(Authenticate::class, 'hasJwt');
        $method->setAccessible(true);
        $result = $method->invokeArgs($auth, ['']);

        $this->assertFalse($result);
    }

    /**
     * @covers PsrJwt\Auth\Authenticate::validate
     * @uses PsrJwt\Auth\Authenticate
     * @uses PsrJwt\Auth\Auth
     * @uses PsrJwt\Factory\Jwt
     * @uses PsrJwt\Validation\Validate
     */
    public function testValidate()
    {
        $jwt = Jwt::builder();
        $token = $jwt->setSecret('Secret123!456$')
            ->setIssuer('localhost')
            ->build()
            ->getToken();

        $auth = new Authenticate('Secret123!456$', 'jwt');

        $method = new ReflectionMethod(Authenticate::class, 'validate');
        $method->setAccessible(true);
        $result = $method->invokeArgs($auth, [$token]);

        $this->assertSame(200, $result->getCode());
        $this->assertSame('Ok', $result->getMessage());
    }

    /**
     * @covers PsrJwt\Auth\Authenticate::validate
     * @uses PsrJwt\Auth\Authenticate
     * @uses PsrJwt\Auth\Auth
     * @uses PsrJwt\Factory\Jwt
     * @uses PsrJwt\Validation\Validate
     */
    public function testValidateBadSecret()
    {
        $jwt = Jwt::builder();
        $token = $jwt->setSecret('Secret123!456$')
            ->setIssuer('localhost')
            ->build()
            ->getToken();

        $auth = new Authenticate('Secret', 'jwt');

        $method = new ReflectionMethod(Authenticate::class, 'validate');
        $method->setAccessible(true);
        $result = $method->invokeArgs($auth, [$token]);

        $this->assertSame(401, $result->getCode());
        $this->assertSame('Unauthorized: Signature is invalid.', $result->getMessage());
    }

    /**
     * @covers PsrJwt\Auth\Authenticate::validate
     * @uses PsrJwt\Auth\Authenticate
     * @uses PsrJwt\Auth\Auth
     * @uses PsrJwt\Factory\Jwt
     * @uses PsrJwt\Validation\Validate
     */
    public function testValidateBadExpiration()
    {
        $jwt = Jwt::builder();
        $token = $jwt->setSecret('Secret123!456$')
            ->setIssuer('localhost')
            ->setPayloadClaim('exp', time() - 10)
            ->build()
            ->getToken();

        $auth = new Authenticate('Secret123!456$', 'jwt');

        $method = new ReflectionMethod(Authenticate::class, 'validate');
        $method->setAccessible(true);
        $result = $method->invokeArgs($auth, [$token]);

        $this->assertSame(401, $result->getCode());
        $this->assertSame('Unauthorized: Expiration claim has expired.', $result->getMessage());
    }

    /**
     * @covers PsrJwt\Auth\Authenticate::validate
     * @uses PsrJwt\Auth\Authenticate
     * @uses PsrJwt\Auth\Auth
     * @uses PsrJwt\Factory\Jwt
     * @uses PsrJwt\Validation\Validate
     */
    public function testValidateBadNotBefore()
    {
        $jwt = Jwt::builder();
        $token = $jwt->setSecret('Secret123!456$')
            ->setIssuer('localhost')
            ->setPayloadClaim('nbf', time() + 60)
            ->build()
            ->getToken();

        $auth = new Authenticate('Secret123!456$', 'jwt');

        $method = new ReflectionMethod(Authenticate::class, 'validate');
        $method->setAccessible(true);
        $result = $method->invokeArgs($auth, [$token]);

        $this->assertSame(401, $result->getCode());
        $this->assertSame('Unauthorized: Not Before claim has not elapsed.', $result->getMessage());
    }

    /**
     * @covers PsrJwt\Auth\Authenticate::validationResponse
     * @uses PsrJwt\Auth\Authenticate::__construct
     * @uses PsrJwt\Auth\Auth
     */
    public function testValidationResponse()
    {
        $auth = new Authenticate('secret', 'jwt');

        $method = new ReflectionMethod(Authenticate::class, 'validationResponse');
        $method->setAccessible(true);
        $result = $method->invokeArgs($auth, [0, 'Ok']);

        $this->assertInstanceOf(Auth::class, $result);
        $this->assertSame(200, $result->getCode());
        $this->assertSame('Ok', $result->getMessage());
    }

    /**
     * @covers PsrJwt\Auth\Authenticate::validationResponse
     * @uses PsrJwt\Auth\Authenticate::__construct
     * @uses PsrJwt\Auth\Auth
     */
    public function testValidationResponseErrors()
    {
        $auth = new Authenticate('secret', 'jwt');

        $method = new ReflectionMethod(Authenticate::class, 'validationResponse');
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
     * @covers PsrJwt\Auth\Authenticate::getToken
     * @uses PsrJwt\Auth\Authenticate
     * @uses PsrJwt\Parser\Parse
     * @uses PsrJwt\Parser\Bearer
     */
    public function testGetToken()
    {
        $request = m::mock(ServerRequestInterface::class);
        $request->shouldReceive('getHeader')
            ->with('authorization')
            ->once()
            ->andReturn(['Bearer abc.def.ghi']);

        $auth = new Authenticate('secret', 'jwt');

        $method = new ReflectionMethod(Authenticate::class, 'getToken');
        $method->setAccessible(true);
        $result = $method->invokeArgs($auth, [$request]);

        $this->assertSame('abc.def.ghi', $result);
    }

    /**
     * @covers PsrJwt\Auth\Authenticate::getToken
     * @uses PsrJwt\Auth\Authenticate
     * @uses PsrJwt\Parser\Parse
     * @uses PsrJwt\Parser\Bearer
     * @uses PsrJwt\Parser\Cookie
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

        $auth = new Authenticate('secret', 'token');

        $method = new ReflectionMethod(Authenticate::class, 'getToken');
        $method->setAccessible(true);
        $result = $method->invokeArgs($auth, [$request]);

        $this->assertSame('abc.123.def', $result);
    }

    /**
     * @covers PsrJwt\Auth\Authenticate::getToken
     * @uses PsrJwt\Auth\Authenticate
     * @uses PsrJwt\Parser\Parse
     * @uses PsrJwt\Parser\Bearer
     * @uses PsrJwt\Parser\Cookie
     * @uses PsrJwt\Parser\Body
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

        $auth = new Authenticate('secret', 'json_token_1');

        $method = new ReflectionMethod(Authenticate::class, 'getToken');
        $method->setAccessible(true);
        $result = $method->invokeArgs($auth, [$request]);

        $this->assertSame('123.abc.def', $result);
    }

    /**
     * @covers PsrJwt\Auth\Authenticate::getToken
     * @uses PsrJwt\Auth\Authenticate
     * @uses PsrJwt\Parser\Parse
     * @uses PsrJwt\Parser\Bearer
     * @uses PsrJwt\Parser\Cookie
     * @uses PsrJwt\Parser\Body
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

        $auth = new Authenticate('secret', 'my_token');

        $method = new ReflectionMethod(Authenticate::class, 'getToken');
        $method->setAccessible(true);
        $result = $method->invokeArgs($auth, [$request]);

        $this->assertSame('ghi.123.xyz', $result);
    }

    /**
     * @covers PsrJwt\Auth\Authenticate::getToken
     * @uses PsrJwt\Auth\Authenticate
     * @uses PsrJwt\Parser\Parse
     * @uses PsrJwt\Parser\Bearer
     * @uses PsrJwt\Parser\Cookie
     * @uses PsrJwt\Parser\Body
     * @uses PsrJwt\Parser\Query
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

        $auth = new Authenticate('secret', 'auth_token');

        $method = new ReflectionMethod(Authenticate::class, 'getToken');
        $method->setAccessible(true);
        $result = $method->invokeArgs($auth, [$request]);

        $this->assertSame('456.gfv.3-1', $result);
    }

    /**
     * @expectedException ReallySimpleJWT\Exception\ValidateException
     * @expectedExceptionMessage JSON Web Token not set.
     * @expectedExceptionCode 11
     * @covers PsrJwt\Auth\Authenticate::getToken
     * @uses PsrJwt\Auth\Authenticate
     * @uses PsrJwt\Parser\Parse
     * @uses PsrJwt\Parser\Bearer
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
        $request->shouldReceive('getCookieParams')
            ->once()
            ->andReturn([]);
        $request->shouldReceive('getQueryParams')
            ->once()
            ->andReturn([]);
        $request->shouldReceive('getParsedBody')
            ->twice()
            ->andReturn([]);

        $auth = new Authenticate('secret', 'jwt');

        $method = new ReflectionMethod(Authenticate::class, 'getToken');
        $method->setAccessible(true);
        $result = $method->invokeArgs($auth, [$request]);
    }

    public function tearDown()
    {
        m::close();
    }
}
