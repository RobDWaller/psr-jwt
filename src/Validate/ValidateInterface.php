<?php

declare(strict_types=1);

namespace PsrJwt\Validate;

use PsrJwt\Status\Status;

interface ValidateInterface
{
    public function validate(): Status;
}