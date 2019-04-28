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
    }
}
