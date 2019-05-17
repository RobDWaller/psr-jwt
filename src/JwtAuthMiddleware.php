<?php

declare(strict_types=1);

namespace PsrJwt;

use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use PsrJwt\Auth\Authenticate;
use PsrJwt\Auth\Auth;

/**
 * Psr-Jwt provides a simple means by which to add JSON Web Token
 * authentication middleware to PSR-7 and PSR-15 compliant frameworks such as
 * Slim PHP and Zend Expressive. It also allows for the generation of JSON
 * Web Tokens via its integration with ReallySimpleJWT.
 *
 * @author Rob Waller <rdwaller1984@gmail.com>
 */
class JwtAuthMiddleware implements MiddlewareInterface
{
    /**
     * @var RequestHandlerInterface
     */
    private $authenticate;

    /**
     * @param RequestHandlerInterface $authenticate
     */
    public function __construct(RequestHandlerInterface $authenticate)
    {
        $this->authenticate = $authenticate;
    }

    /**
     * PSR-7 compliant middleware compatible with frameworks like Slim PHP v3.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable $next
     * @return ResponseInterface
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ): ResponseInterface {
        $auth = $this->authenticate->handle($request);

        if ($auth->getStatusCode() === 200) {
            return $next($request, $response);
        }

        return $auth;
    }

    /**
     * PSR-15 compliant middleware compatible with frameworks like
     * Zend Expressive.
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        
        $auth = $this->authenticate->handle($request);

        if ($auth->getStatusCode() === 200) {
            return $handler->handle($request);
        }

        return $auth;
    }
}
