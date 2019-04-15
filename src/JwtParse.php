<?php

declare(strict_types = 1);

namespace PsrJwt;

use Psr\Http\Message\ServerRequestInterface;

class JwtParse
{
    private const LOCATIONS = [
        'Bearer',
        'Cookie',
        'Query',
        'Body',
        'Server'
    ];

    private $tokenKey;

    public function __construct(string $tokenKey)
    {
        $this->tokenKey = $tokenKey;
    }

    public function findToken(ServerRequestInterface $request): array
    {
        foreach (self::LOCATIONS as $location) {
            $jwtArray = $this->{'getFrom' . $location}($request);

            if (isset($jwtArray[$this->tokenKey])) {
                return $jwtArray;
            }
        }

        return [];
    }

    private function getFromBearer(ServerRequestInterface $request): array
    {
        $authorization = $request->getHeader('authorization');

        $bearer = array_filter($authorization, function ($item) {
            return (bool) preg_match('/^bearer\s.+/', $item);
        });

        $token = explode(' ', $bearer[0] ?? '')[1] ?? '';

        return !empty($token) ? ['jwt' => $token] : [];
    }

    private function getFromCookie(ServerRequestInterface $request): array
    {
        return $request->getCookieParams();
    }

    private function getFromQuery(ServerRequestInterface $request): array
    {
        return $request->getQueryParams();
    }

    private function getFromBody(ServerRequestInterface $request): array
    {
        $body = $request->getParsedBody();

        if (is_array($body) && isset($body[$this->tokenKey])) {
            return $body;
        }

        return $this->parseBodyObject($request);
    }

    private function parseBodyObject(ServerRequestInterface $request): array
    {
        $body = $request->getParsedBody();

        if (is_object($body) && isset($body->{$this->tokenKey})) {
            return [$this->tokenKey => $body->{$this->tokenKey}];
        }

        return [];
    }

    private function getFromServer(ServerRequestInterface $request): array
    {
        return $request->getServerParams();
    }
}
