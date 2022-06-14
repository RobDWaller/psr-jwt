<?php

declare(strict_types=1);

namespace PsrJwt\Validate;

use PsrJwt\Status\Status;
use ReallySimpleJWT\Validate;
use ReallySimpleJWT\Exception\ValidateException;
use ReallySimpleJWT\Exception\ParsedException;

/**
 * Validate the JSON Web Token will parse, has a valid signature, is ready to
 * use and has not expired.
 */
class Validator implements ValidateInterface
{
    private Validate $validate;

    public function __construct(Validate $validate)
    {
        $this->validate = $validate;
    }

    /**
     * The JSON Web Token must be valid and not have expired.
     */
    public function validate(): Status
    {
        try {
            $this->validate->signature()->expiration()->notBefore();
        } catch (ValidateException | ParsedException $e) {
            if (in_array($e->getCode(), [3, 4, 5], true)) {
                return new Status($e->getCode(), $e->getMessage());
            }
        }

        return new Status(0, 'Ok');
    }
}
