<?php

namespace Tests\Parser;

use PHPUnit\Framework\TestCase;
use PsrJwt\Parser\Parse;
use PsrJwt\Parser\Find;
use Psr\Http\Message\ServerRequestInterface;
use Mockery as m;

class FindTest extends TestCase
{
    public function testFind()
    {
        $parse = new Parse(['token_key' => $this->tokenKey]);

        $find = new Find($parse);

        $this->assertInstanceOf(Find::class, $find);
    }
}