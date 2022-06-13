<?php

declare(strict_types=1);

namespace Tests\Auth;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use PsrJwt\Auth\Authorise;
use PsrJwt\Auth\Auth;
use PsrJwt\Factory\Jwt;
use PsrJwt\Parser\ParseException;
use ReflectionMethod;

class AuthoriseTest extends TestCase
{
    /**
     * @covers PsrJwt\Auth\Authorise::__construct
     */
    public function testAuthorise(): void
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
    public function testAuthoriseOk(): void
    {
        $jwt = new Jwt();
        $jwt = $jwt->builder('Secret123!456$');
        $token = $jwt->setIssuer('localhost')
            ->build()
            ->getToken();

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getCookieParams')
            ->willReturn(['foo' => 'bar']);
        $request->expects($this->once())
            ->method('getQueryParams')
            ->willReturn(['jwt' => $token]);
        $request->expects($this->exactly(2))
            ->method('getParsedBody')
            ->willReturn([]);
        $request->expects($this->once())
            ->method('getHeader')
            ->with('authorization')
            ->willReturn([]);

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
    public function testAuthoriseBadRequest(): void
    {
        $jwt = new Jwt();
        $jwt = $jwt->builder('secR3t456!78');

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getCookieParams')
            ->willReturn(['foo' => 'bar']);
        $request->expects($this->once())
            ->method('getQueryParams')
            ->willReturn(['hello' => 'world']);
        $request->expects($this->exactly(2))
            ->method('getParsedBody')
            ->willReturn([]);
        $request->expects($this->once())
            ->method('getHeader')
            ->with('authorization')
            ->willReturn([]);

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
    public function testValidate(): void
    {
        $jwt = new Jwt();
        $jwt = $jwt->builder('Secret123!456$');
        $token = $jwt->setIssuer('localhost')
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
    public function testValidateBadSecret(): void
    {
        $jwt = new Jwt();
        $jwt = $jwt->builder('Secret123!456$');
        $token = $jwt->setIssuer('localhost')
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
    public function testValidateBadExpiration(): void
    {
        $jwt = new Jwt();
        $jwt = $jwt->builder('Secret123!456$');
        $token = $jwt->setIssuer('localhost')
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
    public function testValidateBadNotBefore(): void
    {
        $jwt = new Jwt();
        $jwt = $jwt->builder('Secret123!456$');
        $token = $jwt->setIssuer('localhost')
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
    public function testValidationResponse(): void
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
    public function testValidationResponseErrors(): void
    {
        $auth = new Authorise('secret', 'jwt');

        $method = new ReflectionMethod(Authorise::class, 'validationResponse');
        $method->setAccessible(true);

        $errors = [
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
    public function testGetToken(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getHeader')
            ->with('authorization')
            ->willReturn(['Bearer abc.def.ghi']);

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
    public function testGetTokenCookie(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getHeader')
            ->with('authorization')
            ->willReturn([]);
        $request->expects($this->once())
            ->method('getCookieParams')
            ->willReturn(['token' => 'abc.123.def']);

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
    public function testGetTokenBody(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getHeader')
            ->with('authorization')
            ->willReturn([]);
        $request->expects($this->once())
            ->method('getCookieParams')
            ->willReturn([]);
        $request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn(['json_token_1' => '123.abc.def']);

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
    public function testGetTokenBodyObject(): void
    {
        $token = new \stdClass();
        $token->my_token = 'ghi.123.xyz';

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getHeader')
            ->with('authorization')
            ->willReturn([]);
        $request->expects($this->once())
            ->method('getCookieParams')
            ->willReturn([]);
        $request->expects($this->exactly(2))
            ->method('getParsedBody')
            ->willReturn($token);

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
    public function testGetTokenQuery(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getHeader')
            ->with('authorization')
            ->willReturn([]);
        $request->expects($this->once())
            ->method('getCookieParams')
            ->willReturn([]);
        $request->expects($this->exactly(2))
            ->method('getParsedBody')
            ->willReturn([]);
        $request->expects($this->once())
            ->method('getQueryParams')
            ->willReturn(['auth_token' => '456.gfv.3-1']);

        $auth = new Authorise('secret', 'auth_token');

        $method = new ReflectionMethod(Authorise::class, 'getToken');
        $method->setAccessible(true);
        $result = $method->invokeArgs($auth, [$request]);

        $this->assertSame('456.gfv.3-1', $result);
    }

    /**
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
    public function testGetTokenNoToken(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getHeader')
            ->with('authorization')
            ->willReturn([]);
        $request->expects($this->once())
            ->method('getCookieParams')
            ->willReturn([]);
        $request->expects($this->once())
            ->method('getQueryParams')
            ->willReturn([]);
        $request->expects($this->exactly(2))
            ->method('getParsedBody')
            ->willReturn([]);

        $auth = new Authorise('secret', 'jwt');

        $method = new ReflectionMethod(Authorise::class, 'getToken');
        $method->setAccessible(true);
        $this->expectException(ParseException::class);
        $this->expectExceptionMessage("JSON Web Token not set in request.");
        $method->invokeArgs($auth, [$request]);
    }
}
