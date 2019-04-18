<?php

declare(strict_types = 1);

namespace PsrJwt\Validation;

use ReallySimpleJWT\Parse;
use ReallySimpleJWT\Exception\ValidateException;

class Validate
{
    private $parse;

    public function __construct(Parse $parse)
    {
        $this->parse = $parse;
    }

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
