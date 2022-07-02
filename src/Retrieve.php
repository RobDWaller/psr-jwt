<?php

declare(strict_types=1);

namespace PsrJwt;

use PsrJwt\Location\LocationException;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Find the JSON Web Token within the incoming request object.
 */
class Retrieve
{
    /**
     * @var LocationInterface[] $locations
     */
    private array $locations;

    /**
     * The JSON web token can be found in various parts of the request, a new
     * parser is required to search each part.
     *
     * @param LocationInterface[] $locations
     */
    public function __construct(array $locations)
    {
        $this->locations = $locations;
    }

    /**
     * Search the request for the token. Each parser object is only
     * instantiated if the JWT can't be found in the previous parser object.
     */
    public function findToken(ServerRequestInterface $request): string
    {
        foreach ($this->locations as $location) {
            $token = $location->find($request);
            if (!empty($token)) {
                return $token;
            }
        }

        throw new LocationException('JSON Web Token not set in request.');
    }
}
