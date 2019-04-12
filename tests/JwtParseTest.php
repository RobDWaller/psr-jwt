<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use PsrJwt\JwtParse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionMethod;
use Mockery as m;
use stdClass;

class JwtParseTest extends TestCase
{
    public function testJwtParse()
    {
        $parse = new JwtParse();
        $this->assertInstanceOf(JwtParse::class, $parse);
    }
}
