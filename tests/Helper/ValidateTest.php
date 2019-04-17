<?php

namespace Test\Helper;

use PHPUnit\Framework\TestCase;
use ReallySimpleJWT\Parse;
use PsrJwt\Factory\Jwt;
use PsrJwt\Helper\Validate;

class ValidateTest extends TestCase
{
    /**
     * @covers PsrJwt\Helper\Validate::__construct
     * @uses PsrJwt\Factory\Jwt
     */
    public function testValidate()
    {
        $jwt = Jwt::builder();
        $token = $jwt->setSecret('Secret123!456$')
            ->setIssuer('localhost')
            ->setPayloadClaim('exp', time() - 10)
            ->build()
            ->getToken();

        $validate = new Validate(
            Jwt::parser($token, 'Secret123!456$')
        );

        $this->assertInstanceOf(Validate::class, $validate);
    }

    /**
     * @covers PsrJwt\Helper\Validate::validate
     * @uses PsrJwt\Helper\Validate
     * @uses PsrJwt\Factory\Jwt
     */
    public function testValidateTrue()
    {
        $jwt = Jwt::builder();
        $token = $jwt->setSecret('Secret123!456$')
            ->setIssuer('localhost')
            ->setPayloadClaim('exp', time() - 10)
            ->build()
            ->getToken();

        $validate = new Validate(
            Jwt::parser($token, 'Secret123!456$')
        );

        $result = $validate->validate();

        $this->assertSame(4, $result['code']);
        $this->assertSame('Expiration claim has expired.', $result['message']);
    }

    /**
     * @covers PsrJwt\Helper\Validate::validateNotBefore
     * @uses PsrJwt\Helper\Validate
     * @uses PsrJwt\Factory\Jwt
     */
    public function testValidateNotBefore()
    {
        $jwt = Jwt::builder();
        $token = $jwt->setSecret('Secret123!456$')
            ->setIssuer('localhost')
            ->setPayloadClaim('nbf', time() + 10)
            ->build()
            ->getToken();

        $validate = new Validate(
            Jwt::parser($token, 'Secret123!456$')
        );

        $result = $validate->validateNotBefore(
            ['code' => 0, 'message' => 'Ok']
        );

        $this->assertSame(5, $result['code']);
        $this->assertSame('Not Before claim has not elapsed.', $result['message']);
    }
}
