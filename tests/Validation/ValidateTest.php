<?php

namespace Tests\Validation;

use PHPUnit\Framework\TestCase;
use ReallySimpleJWT\Validate as RSValidate;
use PsrJwt\Factory\Jwt;
use PsrJwt\Validation\Validate;

class ValidateTest extends TestCase
{
    /**
     * @covers PsrJwt\Validation\Validate::__construct
     * @uses PsrJwt\Factory\Jwt
     */
    public function testValidate(): void
    {
        $jwt = new Jwt();
        $builder = $jwt->builder('Secret123!456$');
        $token = $builder->setIssuer('localhost')
            ->setPayloadClaim('exp', time() - 10)
            ->build()
            ->getToken();

        $validate = new Validate(
            $jwt->validator($token, 'Secret123!456$')
        );

        $this->assertInstanceOf(Validate::class, $validate);
    }

    /**
     * @covers PsrJwt\Validation\Validate::validate
     * @uses PsrJwt\Validation\Validate
     * @uses PsrJwt\Factory\Jwt
     */
    public function testValidateOk(): void
    {
        $jwt = new Jwt();
        $builder = $jwt->builder('Secret123!456$');
        $token = $builder->setIssuer('localhost')
            ->setPayloadClaim('exp', time() + 10)
            ->build()
            ->getToken();

        $validate = new Validate(
            $jwt->validator($token, 'Secret123!456$')
        );

        $result = $validate->validate();

        $this->assertSame(0, $result['code']);
        $this->assertSame('Ok', $result['message']);
    }

    /**
     * @covers PsrJwt\Validation\Validate::validate
     * @uses PsrJwt\Validation\Validate
     * @uses PsrJwt\Factory\Jwt
     */
    public function testValidateExpiration(): void
    {
        $jwt = new Jwt();
        $builder = $jwt->builder('Secret123!456$');
        $token = $builder->setIssuer('localhost')
            ->setPayloadClaim('exp', time() - 10)
            ->build()
            ->getToken();

        $validate = new Validate(
            $jwt->validator($token, 'Secret123!456$')
        );

        $result = $validate->validate();

        $this->assertSame(4, $result['code']);
        $this->assertSame('Expiration claim has expired.', $result['message']);
    }
    
    /**
     * @covers PsrJwt\Validation\Validate::validate
     * @uses PsrJwt\Validation\Validate
     * @uses PsrJwt\Factory\Jwt::validator
     */
    public function testValidateBadSignature(): void
    {
        $jwt = new Jwt();

        $validate = new Validate(
            $jwt->validator('123.abc.456', 'Secret123!456$')
        );

        $result = $validate->validate();

        $this->assertSame(3, $result['code']);
        $this->assertSame('Signature is invalid.', $result['message']);
    }

    /**
     * @covers PsrJwt\Validation\Validate::validateNotBefore
     * @uses PsrJwt\Validation\Validate
     * @uses PsrJwt\Factory\Jwt
     */
    public function testValidateNotBefore(): void
    {
        $jwt = new Jwt();
        $builder = $jwt->builder('Secret123!456$');
        $token = $builder->setIssuer('localhost')
            ->setPayloadClaim('nbf', time() + 10)
            ->build()
            ->getToken();

        $validate = new Validate(
            $jwt->validator($token, 'Secret123!456$')
        );

        $result = $validate->validateNotBefore(
            ['code' => 0, 'message' => 'Ok']
        );

        $this->assertSame(5, $result['code']);
        $this->assertSame('Not Before claim has not elapsed.', $result['message']);
    }

    /**
     * @covers PsrJwt\Validation\Validate::validateNotBefore
     * @uses PsrJwt\Validation\Validate
     * @uses PsrJwt\Factory\Jwt
     */
    public function testValidateNotBeforeOk(): void
    {
        $jwt = new Jwt();
        $builder = $jwt->builder('Secret123!456$');
        $token = $builder->setIssuer('localhost')
            ->setPayloadClaim('nbf', time() - 20)
            ->build()
            ->getToken();

        $validate = new Validate(
            $jwt->validator($token, 'Secret123!456$')
        );

        $result = $validate->validateNotBefore(
            ['code' => 0, 'message' => 'Ok']
        );

        $this->assertSame(0, $result['code']);
        $this->assertSame('Ok', $result['message']);
    }
}
