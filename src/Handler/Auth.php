<?php

declare(strict_types=1);

namespace PsrJwt\Handler;

use PsrJwt\Auth\Authenticate;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Nyholm\Psr7\Response;

/**
 * Default JWT authentication handler. Allows you to customise body response
 * with a simple message. If you require a more detailed response create your
 * own handler which extends the Authenticate class and calls the authenticate
 * method as below. 
 */
class Auth extends Authenticate implements RequestHandlerInterface
{
    /**
     * @var string The content to add to the response body.
     */
    private $body;

    /**
     * @param string $secret
     * @param string $tokenKey
     * @param string $body
     */
    public function __construct(string $secret, string $tokenKey, string $body)
    {
        parent::__construct($secret, $tokenKey);

        $this->body = $body;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
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
