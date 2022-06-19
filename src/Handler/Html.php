<?php

declare(strict_types=1);

namespace PsrJwt\Handler;

use PsrJwt\Auth\Authorise;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Nyholm\Psr7\Response;
use PsrJwt\Retrieve;

/**
 * JWT authorisation handler which returns a text/html response on
 * authorisation failure. Allows you to customise the body response with a
 * simple message.
 */
class Html implements RequestHandlerInterface
{
    private Config $config;

    private Retrieve $retrieve;

    private Authorise $authorise;

    public function __construct(Config $config, Retrieve $retrieve, Authorise $authorise)
    {
        $this->config = $config;

        $this->retrieve = $retrieve;

        $this->authorise = $authorise;
    }

    /**
     * Handle the authorisation process and generate the relevant text / html
     * response and code.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $token = $this->retrieve->findToken($request);
        $auth = $this->authorise->authorise($token);

        return new Response(
            $auth->getCode(),
            ['Content-Type' => 'text/html'],
            $this->body,
            '1.1',
            $auth->getMessage()
        );
    }
}
