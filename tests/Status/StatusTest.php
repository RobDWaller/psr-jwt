<?php

declare(strict_types=1);

namespace Tests\Validation;

use PHPUnit\Framework\TestCase;
use PsrJwt\Status\Status;

class StatusTest extends TestCase
{
    /**
     * @covers PsrJwt\Status\Status::getCode
     */
    public function testGetCode(): void
    {
        $status = new Status(200, "");

        $this->assertSame(200, $status->getCode());
    }

    /**
     * @covers PsrJwt\Status\Status::getMessage
     */
    public function testGetMessage(): void
    {
        $status = new Status(400, "Hello World");

        $this->assertSame("Hello World", $status->getMessage());
    }
}