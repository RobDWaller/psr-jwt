<?php

declare(strict_types=1);

namespace Tests\Handler;

use PHPUnit\Framework\TestCase;
use PsrJwt\Handler\Config;

class ConfigTest extends TestCase
{
    /**
     * @covers PsrJwt\Handler\Config::getSecret
     */
    public function testGetSecret(): void
    {
        $config = new Config('123');

        $this->assertSame('123', $config->getSecret());
    }
}