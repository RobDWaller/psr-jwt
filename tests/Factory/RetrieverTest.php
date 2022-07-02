<?php

declare(strict_types=1);

namespace Tests\Factory;

use PHPUnit\Framework\TestCase;
use PsrJwt\Factory\Retriever;
use PsrJwt\Retrieve;

class RetrieverTest extends TestCase
{
    /**
     * @covers PsrJwt\Factory\Retriever::make
     */
    public function testMake(): void
    {
        $this->assertInstanceOf(Retrieve::class, Retriever::make('jwt'));
    }
}
