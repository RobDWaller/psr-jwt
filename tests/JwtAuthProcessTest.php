<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use PsrJwt\JwtAuthProcess;

class JwtAuthProcessTest extends TestCase
{
    public function testJwtAuthProcess()
    {
        $process = new JwtAuthProcess();

        $this->assertInstanceOf(JwtAuthProcess::class, $process);
    }
}
