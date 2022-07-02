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
        $config = new Config('123', 'Bar');

        $this->assertSame('123', $config->getSecret());
    }

    /**
     * @covers PsrJwt\Handler\Config::getResponse
     */
    public function testGetResponseAsString(): void
    {
        $config = new Config('456', 'Hello');

        $this->assertSame('Hello', $config->getResponse());
    }

    /**
     * @covers PsrJwt\Handler\Config::getResponse
     */
    public function testGetResponseAsArray(): void
    {
        $config = new Config('456', ['Hello', 'World']);

        $this->assertCount(2, $config->getResponse());
        $this->assertSame('Hello', $config->getResponse()[0]);
        $this->assertSame('World', $config->getResponse()[1]);
    }
}
