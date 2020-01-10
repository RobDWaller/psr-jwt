<?php

declare(strict_types=1);

namespace PsrJwt;

use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use PsrJwt\Auth\Authorise;
use PsrJwt\Auth\Auth;

/**
 * Psr-Jwt provides a simple means by which to add JSON Web Token
 * authorisation middleware to PSR-7 and PSR-15 compliant frameworks such as
 * Slim PHP. It also allows for the generation of JSON Web Tokens via its
 * integration with ReallySimpleJWT.
 *
 * @author Rob Waller <rdwaller1984@gmail.com>
 */
class JwtAuthMiddleware implements MiddlewareInterface
{
    /**
     * @var RequestHandlerInterface
     */
    private $authorise;

    /**
     * @param RequestHandlerInterface $authorise
     */
    public function __construct(RequestHandlerInterface $authorise)
    {
        $this->authorise = $authorise;
    }

    /**
     * PSR-7 compliant middleware compatible with frameworks like Slim PHP.
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
        $authResponse = $this->authorise->handle($request);

        if ($authResponse->getStatusCode() === 200) {
            return $next($request, $response);
        }

        return $authResponse;
    }

    /**
     * PSR-15 compliant middleware method.
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $authResponse = $this->authorise->handle($request);

        if ($authResponse->getStatusCode() === 200) {
            return $handler->handle($request);
        }

        return $authResponse;
    }
}
