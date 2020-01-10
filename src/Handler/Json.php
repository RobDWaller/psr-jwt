<?php

declare(strict_types=1);

namespace PsrJwt\Handler;

use PsrJwt\Auth\Authorise;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Nyholm\Psr7\Response;

/**
 * JWT authorisation handler which returns a application/json response on
 * authorisation failure. Allows you to customise the body response with a
 * simple message.
 */
class Json extends Authorise implements RequestHandlerInterface
{
    /**
     * @var array The content to add to the response body.
     */
    private $body;

    /**
     * @param string $secret
     * @param string $tokenKey
     * @param array $body
     */
    public function __construct(string $secret, string $tokenKey, array $body)
    {
        parent::__construct($secret, $tokenKey);

        $this->body = $body;
    }

    /**
     * Handle the authorisation process and generate the relevant json
     * response and code.
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $auth = $this->authorise($request);

        return new Response(
            $auth->getCode(),
            ['Content-Type' => 'application/json'],
            (string) json_encode($this->body),
            '1.1',
            $auth->getMessage()
        );
    }
}
