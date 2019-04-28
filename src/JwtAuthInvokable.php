<?php

declare(strict_types=1);

namespace PsrJwt;

use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use PsrJwt\Auth\Authenticate;

class JwtAuthInvokable
{
    private $authenticate;

    public function __construct(Authenticate $authenticate)
    {
        $this->authenticate = $authenticate;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ): ResponseInterface {
        $auth = $this->authenticate->authenticate($request);

        if ($auth->getCode() === 200) {
            return $next($request, $response);
        }

        $factory = new Psr17Factory();
        return $factory->createResponse($auth->getCode(), $auth->getMessage());
    }
}
