<?php

declare(strict_types=1);

namespace Tests\Auth;

use PHPUnit\Framework\TestCase;
use PsrJwt\Auth\Authorise;
use PsrJwt\Factory\Jwt;

class AuthoriseTest extends TestCase
{
    /**
     * @covers PsrJwt\Auth\Authorise::authorise
     */
    public function testAuthorise(): void
    {
        $jwt = new Jwt();
        $builder = $jwt->builder('Secret123!456$');
        $token = $builder->setIssuer('localhost')
            ->setPayloadClaim('exp', time() + 10)
            ->build()
            ->getToken();

        $authorise = new Authorise();
        $result = $authorise->authorise($token, 'Secret123!456$');

        $this->assertSame(200, $result->getCode());
        $this->assertSame('Ok', $result->getMessage());
    }

    /**
     * @covers PsrJwt\Auth\Authorise::authorise
     */
    public function testAuthoriseExpiration(): void
    {
        $jwt = new Jwt();
        $builder = $jwt->builder('Secret123!456$');
        $token = $builder->setIssuer('localhost')
            ->setPayloadClaim('exp', time() - 10)
            ->build()
            ->getToken();

        $authorise = new Authorise();
        $result = $authorise->authorise($token, 'Secret123!456$');

        $this->assertSame(403, $result->getCode());
        $this->assertSame('Forbidden: Expiration claim has expired.', $result->getMessage());
    }

    /**
     * @covers PsrJwt\Auth\Authorise::authorise
     */
    public function testAuthoriseBadSignature(): void
    {
        $jwt = new Jwt();

        $authorise = new Authorise();
        $result = $authorise->authorise('123.abc.456', 'Secret123!456$');

        $this->assertSame(401, $result->getCode());
        $this->assertSame('Unauthorized: Signature is invalid.', $result->getMessage());
    }

    /**
     * @covers PsrJwt\Auth\Authorise::authorise
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

        $authorise = new Authorise();
        $result = $authorise->authorise($token, 'Secret123!456$');

        $this->assertSame(403, $result->getCode());
        $this->assertSame('Forbidden: Not Before claim has not elapsed.', $result->getMessage());
    }

    /**
     * @covers PsrJwt\Auth\Authorise::authorise
     */
    public function testValidateNotBeforeOk(): void
    {
        $jwt = new Jwt();
        $builder = $jwt->builder('Secret123!456$');
        $token = $builder->setIssuer('localhost')
            ->setPayloadClaim('nbf', time() - 20)
            ->build()
            ->getToken();

        $authorise = new Authorise();
        $result = $authorise->authorise($token, 'Secret123!456$');

        $this->assertSame(200, $result->getCode());
        $this->assertSame('Ok', $result->getMessage());
    }
}
