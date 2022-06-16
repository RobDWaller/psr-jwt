<?php

declare(strict_types=1);

namespace Tests\Auth;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use PsrJwt\Auth\Authorise;
use PsrJwt\Auth\AuthoriseInterface;
use PsrJwt\Factory\Jwt;
use PsrJwt\Parser\ParseException;
use ReflectionMethod;

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

        $this->assertSame(400, $result->getCode());
        $this->assertSame('Bad Request: Expiration claim has expired.', $result->getMessage());
    }
}
