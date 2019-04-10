<?php

namespace Test;

use PHPUnit\Framework\TestCase;
use ReallySimpleJWT\Parse;
use PsrJwt\JwtFactory;
use PsrJwt\JwtValidate;

class JwtValidateTest extends TestCase
{
    /**
     * @covers PsrJwt\JwtValidate::__construct
     * @uses PsrJwt\JwtFactory::builder
     * @uses PsrJwt\JwtFactory::parser
     */
    public function testJwtValidate()
    {
        $jwt = JwtFactory::builder();
        $token = $jwt->setSecret('Secret123!456$')
            ->setIssuer('localhost')
            ->setPayloadClaim('exp', time() - 10)
            ->build()
            ->getToken();

        $validate = new JwtValidate(
            JwtFactory::parser($token, 'Secret123!456$')
        );

        $this->assertInstanceOf(JwtValidate::class, $validate);
    }

    /**
     * @covers PsrJwt\JwtValidate::validate
     * @uses PsrJwt\JwtValidate::__construct
     * @uses PsrJwt\JwtFactory::builder
     * @uses PsrJwt\JwtFactory::parser
     */
    public function testValidate()
    {
        $jwt = JwtFactory::builder();
        $token = $jwt->setSecret('Secret123!456$')
            ->setIssuer('localhost')
            ->setPayloadClaim('exp', time() - 10)
            ->build()
            ->getToken();

        $validate = new JwtValidate(
            JwtFactory::parser($token, 'Secret123!456$')
        );

        $result = $validate->validate();

        $this->assertSame(4, $result['code']);
        $this->assertSame('Expiration claim has expired.', $result['message']);
    }

    /**
     * @covers PsrJwt\JwtValidate::validateNotBefore
     * @uses PsrJwt\JwtValidate::__construct
     * @uses PsrJwt\JwtFactory::builder
     * @uses PsrJwt\JwtFactory::parser
     */
    public function testValidateNotBefore()
    {
        $jwt = JwtFactory::builder();
        $token = $jwt->setSecret('Secret123!456$')
            ->setIssuer('localhost')
            ->setPayloadClaim('nbf', time() + 10)
            ->build()
            ->getToken();

        $validate = new JwtValidate(
            JwtFactory::parser($token, 'Secret123!456$')
        );

        $result = $validate->validateNotBefore(
            ['code' => 0, 'message' => 'Ok']
        );

        $this->assertSame(5, $result['code']);
        $this->assertSame('Not Before claim has not elapsed.', $result['message']);
    }
}
