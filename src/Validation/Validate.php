<?php

declare(strict_types=1);

namespace PsrJwt\Validation;

use ReallySimpleJWT\Parse;
use ReallySimpleJWT\Exception\ValidateException;

/**
 * Validate the JSON Web Token will parse, has a valid signature, is ready to
 * use and has not expired.
 */
class Validate
{
    /**
     * @param Parse $parse
     */
    private $parse;

    /**
     * @param Parse $parse
     */
    public function __construct(Parse $parse)
    {
        $this->parse = $parse;
    }

    /**
     * The JSON Web Token must be valid and not have expired.
     *
     * @return array
     */
    public function validate(): array
    {
        try {
            $this->parse->validate()
                ->validateExpiration();
        } catch (ValidateException $e) {
            if (in_array($e->getCode(), [1, 2, 3, 4], true)) {
                return ['code' => $e->getCode(), 'message' => $e->getMessage()];
            }
        }

        return ['code' => 0, 'message' => 'Ok'];
    }

    /**
     * The token must be ready to use.
     *
     * @return array
     */
    public function validateNotBefore(array $validationState): array
    {
        try {
            $this->parse->validateNotBefore();
        } catch (ValidateException $e) {
            if ($e->getCode()  === 5) {
                return ['code' => $e->getCode(), 'message' => $e->getMessage()];
            }
        }

        return $validationState;
    }
}
