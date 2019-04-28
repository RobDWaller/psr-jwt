<?php

namespace Tests\Auth;

use PHPUnit\Framework\TestCase;
use PsrJwt\Auth\Authenticate;

class AuthenticateTest extends TestCase
{
    public function testAuthenticate()
    {
        $auth = new Authenticate('jwt', 'secret');
        $this->assertInstanceOf(Authenticate::class, $auth);
    }
}
