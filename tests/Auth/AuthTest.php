<?php

namespace Tests\Auth;

use PHPUnit\Framework\TestCase;
use PsrJwt\Auth\Auth;

class AuthTest extends TestCase
{
    /**
     * @covers PsrJwt\Auth\Auth::__construct
     */
    public function testAuth(): Auth
    {
        $auth = new Auth(200, 'Ok');
        $this->assertInstanceOf(Auth::class, $auth);
        return $auth;
    }

    /**
     * @depends testAuth
     * @covers PsrJwt\Auth\Auth::getCode
     */
    public function testGetCode(Auth $auth): void
    {
        $this->assertSame(200, $auth->getCode());
    }

    /**
     * @depends testAuth
     * @covers PsrJwt\Auth\Auth::getMessage
     */
    public function testGetMessage(Auth $auth): void
    {
        $this->assertSame('Ok', $auth->getMessage());
    }
}
