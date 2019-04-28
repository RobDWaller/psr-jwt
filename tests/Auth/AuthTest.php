<?php

namespace Tests\Auth;

use PHPUnit\Framework\TestCase;
use PsrJwt\Auth\Auth;

class AuthTest extends TestCase
{
    public function testAuth()
    {
        $auth = new Auth(200, 'Ok');
        $this->assertInstanceOf(Auth::class, $auth);
        return $auth;
    }

    /**
     * @depends testAuth
     */
    public function testGetCode($auth)
    {
        $this->assertSame(200, $auth->getCode());
    }

    /**
     * @depends testAuth
     */
    public function testGetMessage($auth)
    {
        $this->assertSame('Ok', $auth->getMessage());
    }
}
