<?php

declare(strict_types=1);

namespace PsrJwt;

use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use PsrJwt\Auth\Authenticate;

class JwtAuthMiddleware implements MiddlewareInterface
{
    private $authenticate;

    public function __construct(Authenticate $authenticate)
    {
        $this->authenticate = $authenticate;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $auth = $this->authenticate->authenticate($request);

        if ($auth->getCode() === 200) {
            return $handler->handle($request);
        }

        $factory = new Psr17Factory();
        return $factory->createResponse($auth->getCode(), $auth->getMessage());
    }
}
