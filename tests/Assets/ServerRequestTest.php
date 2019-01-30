<?php

namespace Tests\Assets;

use PHPUnit\Framework\TestCase;
use Tests\Assets\ServerRequest;

class ServerRequestTest extends TestCase
{
    public function testServerRequest()
    {
        $server = [];
        $cookies = [];
        $query = [];
        $body = [];

        $request = new ServerRequest($server, $cookies, $query, $body);

        $this->assertInstanceOf(ServerRequest::class, $request);
    }

    public function testGetServer()
    {
        $server = ['one' => 'two'];
        $cookies = [3 => 4];
        $query = ['five' => 'six'];
        $body = [7 => 8];

        $request = new ServerRequest($server, $cookies, $query, $body);

        $this->assertSame($request->getServerParams()['one'], 'two');
    }

    public function testGetCookies()
    {
        $server = ['one' => 'two'];
        $cookies = [3 => 4];
        $query = ['five' => 'six'];
        $body = [7 => 8];

        $request = new ServerRequest($server, $cookies, $query, $body);

        $this->assertSame($request->getCookieParams()[3], 4);
    }

    public function testGetQuery()
    {
        $server = ['one' => 'two'];
        $cookies = [3 => 4];
        $query = ['five' => 'six'];
        $body = [7 => 8];

        $request = new ServerRequest($server, $cookies, $query, $body);

        $this->assertSame($request->getQueryParams()['five'], 'six');
    }

    public function testGetBody()
    {
        $server = ['one' => 'two'];
        $cookies = [3 => 4];
        $query = ['five' => 'six'];
        $body = [7 => 8];

        $request = new ServerRequest($server, $cookies, $query, $body);

        $this->assertSame($request->getParsedBody()[7], 8);
    }

    public function testGetBodyAsObject()
    {
        $server = ['one' => 'two'];
        $cookies = [3 => 4];
        $query = ['five' => 'six'];

        $body = new \stdClass();
        $body->seven = 8;

        $request = new ServerRequest($server, $cookies, $query, $body);

        $this->assertSame($request->getParsedBody()->seven, 8);
    }

    public function testGetBodyNull()
    {
        $server = ['one' => 'two'];
        $cookies = [3 => 4];
        $query = ['five' => 'six'];
        $body = null;

        $request = new ServerRequest($server, $cookies, $query, $body);

        $this->assertNull($request->getParsedBody());
    }
}
