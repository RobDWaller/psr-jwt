<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use PsrJwt\JwtAuthProcess;

class JwtAuthProcessTest extends TestCase
{
    /**
     * @covers PsrJwt\JwtAuthProcess
     */
    public function testJwtAuthProcess()
    {
        $process = new JwtAuthProcess();

        $this->assertInstanceOf(JwtAuthProcess::class, $process);
    }
}
