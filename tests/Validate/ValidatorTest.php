<?php

declare(strict_types=1);

namespace Tests\Validate;

use PHPUnit\Framework\TestCase;
use PsrJwt\Factory\Jwt;
use PsrJwt\Validate\Validator;

class ValidatorTest extends TestCase
{
    /**
     * @covers PsrJwt\Validation\Validate::__construct
     * @uses PsrJwt\Factory\Jwt
     */
    public function testValidator(): void
    {
        $jwt = new Jwt();
        $builder = $jwt->builder('Secret123!456$');
        $token = $builder->setIssuer('localhost')
            ->setPayloadClaim('exp', time() - 10)
            ->build()
            ->getToken();

        $validator = new Validator(
            $jwt->validator($token, 'Secret123!456$')
        );

        $this->assertInstanceOf(Validator::class, $validator);
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

        $validator = new Validator(
            $jwt->validator($token, 'Secret123!456$')
        );

        $result = $validator->validate();

        $this->assertSame(0, $result->getCode());
        $this->assertSame('Ok', $result->getMessage());
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

        $validator = new Validator(
            $jwt->validator($token, 'Secret123!456$')
        );

        $result = $validator->validate();

        $this->assertSame(4, $result->getCode());
        $this->assertSame('Expiration claim has expired.', $result->getMessage());
    }

    /**
     * @covers PsrJwt\Validation\Validate::validate
     * @uses PsrJwt\Validation\Validate
     * @uses PsrJwt\Factory\Jwt::validator
     */
    public function testValidateBadSignature(): void
    {
        $jwt = new Jwt();

        $validator = new Validator(
            $jwt->validator('123.abc.456', 'Secret123!456$')
        );

        $result = $validator->validate();

        $this->assertSame(3, $result->getCode());
        $this->assertSame('Signature is invalid.', $result->getMessage());
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
            ->setExpiration(time() + 20)
            ->setPayloadClaim('nbf', time() + 10)
            ->build()
            ->getToken();

        $validator = new Validator(
            $jwt->validator($token, 'Secret123!456$')
        );

        $result = $validator->validate();

        $this->assertSame(5, $result->getCode());
        $this->assertSame('Not Before claim has not elapsed.', $result->getMessage());
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

        $validator = new Validator(
            $jwt->validator($token, 'Secret123!456$')
        );

        $result = $validator->validate();

        $this->assertSame(0, $result->getCode());
        $this->assertSame('Ok', $result->getMessage());
    }
}
