<?php

declare(strict_types=1);

namespace PsrJwt;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use PsrJwt\JwtAuthHandler;

class JwtAuthInvokable
{
    private $handler;

    public function __construct(JwtAuthHandler $handler)
    {
        $this->handler = $handler;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ): ResponseInterface {
        return $next($request, $this->handler->handle($request));
    }
}
