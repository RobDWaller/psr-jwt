<?php

declare(strict_types=1);

namespace PsrJwt\Validation;

use ReallySimpleJWT\Validate as RSValidate;
use ReallySimpleJWT\Exception\ValidateException;
use ReallySimpleJWT\Exception\ParseException;

/**
 * Validate the JSON Web Token will parse, has a valid signature, is ready to
 * use and has not expired.
 */
class Validate
{
    /**
     * @param RSValidate $validate
     */
    private RSValidate $validate;

    public function __construct(RSValidate $validate)
    {
        $this->validate = $validate;
    }

    /**
     * The JSON Web Token must be valid and not have expired.
     *
     * @return mixed[]
     */
    public function validate(): array
    {
        try {
            $this->validate->structure()
                ->signature()
                ->expiration();
        } catch (ValidateException | ParseException $e) {
            if (in_array($e->getCode(), [1, 2, 3, 4], true)) {
                return ['code' => $e->getCode(), 'message' => $e->getMessage()];
            }
        }

        return ['code' => 0, 'message' => 'Ok'];
    }

    /**
     * The token must be ready to use.
     *
     * @param mixed[] $validationState
     * @return mixed[]
     */
    public function validateNotBefore(array $validationState): array
    {
        try {
            $this->validate->notBefore();
        } catch (ValidateException | ParseException $e) {
            if ($e->getCode()  === 5) {
                return ['code' => $e->getCode(), 'message' => $e->getMessage()];
            }
        }

        return $validationState;
    }
}
