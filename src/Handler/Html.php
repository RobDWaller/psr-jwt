<?php

declare(strict_types=1);

namespace PsrJwt\Handler;

use PsrJwt\Auth\Authorise;
use PsrJwt\Location\LocationException;
use PsrJwt\Status\Status;
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
        try {
            $token = $this->retrieve->findToken($request);
            $status = $this->authorise->authorise($token, $this->config->getSecret());
        } catch (LocationException $e) {
            $status = new Status(401, 'Unauthorized: ' . $e->getMessage());
        }

        $response = $this->config->getResponse();

        return new Response(
            $status->getCode(),
            ['Content-Type' => 'text/html'],
            is_string($response) ? $response : '',
            '1.1',
            $status->getMessage()
        );
    }
}
