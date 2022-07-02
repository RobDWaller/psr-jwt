<?php

declare(strict_types=1);

namespace Tests\Factory;

use PHPUnit\Framework\TestCase;
use PsrJwt\Handler\Html;
use PsrJwt\Handler\Json;
use PsrJwt\Factory\Handler;

class HandlerTest extends TestCase
{
    /**
     * @covers PsrJwt\Factory\Handler::html
     */
    public function testHtml(): void
    {
        $handler = Handler::html('key', 'secret', 'response');

        $this->assertInstanceOf(Html::class, $handler);
    }

    /**
     * @covers PsrJwt\Factory\Handler::json
     */
    public function testJson(): void
    {
        $handler = Handler::json('key', 'secret', ['response']);

        $this->assertInstanceOf(Json::class, $handler);
    }
}
