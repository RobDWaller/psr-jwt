<?php

namespace Test;

use PHPUnit\Framework\TestCase;
use PsrJwt\JwtAuthInvokable;

class JwtAuthInvokableTest extends TestCase
{
    public function testJwtAuthInokable()
    {
        $invokable = new JwtAuthInvokable();

        $this->assertInstanceOf(JwtAuthInvokable::class, $invokable);
    }
}
