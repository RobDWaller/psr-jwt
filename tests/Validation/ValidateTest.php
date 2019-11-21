<?php

namespace Tests\Validation;

use PHPUnit\Framework\TestCase;
use ReallySimpleJWT\Parse;
use PsrJwt\Factory\Jwt;
use PsrJwt\Validation\Validate;

class ValidateTest extends TestCase
{
    /**
     * @covers PsrJwt\Validation\Validate::__construct
     * @uses PsrJwt\Factory\Jwt
     */
    public function testValidate()
    {
        $jwt = new Jwt();
        $builder = $jwt->builder();
        $token = $builder->setSecret('Secret123!456$')
            ->setIssuer('localhost')
            ->setPayloadClaim('exp', time() - 10)
            ->build()
            ->getToken();

        $validate = new Validate(
            $jwt->parser($token, 'Secret123!456$')
        );

        $this->assertInstanceOf(Validate::class, $validate);
    }

    /**
     * @covers PsrJwt\Validation\Validate::validate
     * @uses PsrJwt\Validation\Validate
     * @uses PsrJwt\Factory\Jwt
     */
    public function testValidateOk()
    {
        $jwt = new Jwt();
        $builder = $jwt->builder();
        $token = $builder->setSecret('Secret123!456$')
            ->setIssuer('localhost')
            ->setPayloadClaim('exp', time() + 10)
            ->build()
            ->getToken();

        $validate = new Validate(
            $jwt->parser($token, 'Secret123!456$')
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
    public function testValidateExpiration()
    {
        $jwt = new Jwt();
        $builder = $jwt->builder();
        $token = $builder->setSecret('Secret123!456$')
            ->setIssuer('localhost')
            ->setPayloadClaim('exp', time() - 10)
            ->build()
            ->getToken();

        $validate = new Validate(
            $jwt->parser($token, 'Secret123!456$')
        );

        $result = $validate->validate();

        $this->assertSame(4, $result['code']);
        $this->assertSame('Expiration claim has expired.', $result['message']);
    }

    /**
     * @covers PsrJwt\Validation\Validate::validate
     * @uses PsrJwt\Validation\Validate
     * @uses PsrJwt\Factory\Jwt::parser
     */
    public function testValidateTokenStructure()
    {
        $jwt = new Jwt();

        $validate = new Validate(
            $jwt->parser('123.abc', 'Secret123!456$')
        );

        $result = $validate->validate();

        $this->assertSame(1, $result['code']);
        $this->assertSame('Token is invalid.', $result['message']);
    }

    /**
     * @covers PsrJwt\Validation\Validate::validate
     * @uses PsrJwt\Validation\Validate
     * @uses PsrJwt\Factory\Jwt::parser
     */
    public function testValidateBadSignature()
    {
        $jwt = new Jwt();

        $validate = new Validate(
            $jwt->parser('123.abc.456', 'Secret123!456$')
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
    public function testValidateNotBefore()
    {
        $jwt = new Jwt();
        $builder = $jwt->builder();
        $token = $builder->setSecret('Secret123!456$')
            ->setIssuer('localhost')
            ->setPayloadClaim('nbf', time() + 10)
            ->build()
            ->getToken();

        $validate = new Validate(
            $jwt->parser($token, 'Secret123!456$')
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
    public function testValidateNotBeforeOk()
    {
        $jwt = new Jwt();
        $builder = $jwt->builder();
        $token = $builder->setSecret('Secret123!456$')
            ->setIssuer('localhost')
            ->setPayloadClaim('nbf', time() - 20)
            ->build()
            ->getToken();

        $validate = new Validate(
            $jwt->parser($token, 'Secret123!456$')
        );

        $result = $validate->validateNotBefore(
            ['code' => 0, 'message' => 'Ok']
        );

        $this->assertSame(0, $result['code']);
        $this->assertSame('Ok', $result['message']);
    }
}
