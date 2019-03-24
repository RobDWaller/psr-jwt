<?php

declare(strict_types=1);

namespace PsrJwt;

use PsrJwt\JwtAuth;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class JwtAuthProcess extends JwtAuth implements MiddlewareInterface
{
    public function __construct(string $secret)
    {
        parent::__construct($secret);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $token = $this->getToken($request);

        $this->validate($token);

        return $handler->handle($request);
    }
}
