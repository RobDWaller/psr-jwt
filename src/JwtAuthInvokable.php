<?php

declare(strict_types=1);

namespace PsrJwt;
use PsrJwt\JwtAuth;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class JwtAuthInvokable extends JwtAuth
{
    public function __construct(string $secret)
    {
        parent::__construct($secret);
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next): ResponseInterface
    {
        return $response;
    }
}
