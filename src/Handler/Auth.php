<?php

declare(strict_types=1);

namespace PsrJwt\Handler;

use PsrJwt\Auth\Authenticate;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Nyholm\Psr7\Response;

class Auth extends Authenticate implements RequestHandlerInterface
{
    private $body;

    public function __construct(string $secret, string $tokenKey, string $body)
    {
        parent::__construct($secret, $tokenKey);

        $this->body = $body;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $auth = $this->authenticate($request);

        return new Response(
            $auth->getCode(),
            [],
            $this->body,
            '1.1',
            $auth->getMessage()
        );
    }
}
