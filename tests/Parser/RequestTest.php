<?php

namespace Tests\Parser;

use PHPUnit\Framework\TestCase;
use PsrJwt\Parser\Parse;
use PsrJwt\Parser\Request;
use Psr\Http\Message\ServerRequestInterface;
use Mockery as m;

class RequestTest extends TestCase
{
    public function testRequest()
    {
        $parse = new Parse(['token_key' => 'jwt']);

        $request = new Request($parse);

        $this->assertInstanceOf(Request::class, $request);
    }
}