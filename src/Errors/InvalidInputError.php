<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Errors;

/**
 * Created when an error is returned by the carrier, that reflects that a specific input has an invalid value.
 * For instance, when first name is too long or a number is expected, but a string is given.
 */
class InvalidInputError extends AbstractCarrierError
{
    public function __construct(string $errorCode, string $detail, string $pointer = null)
    {
        parent::__construct($errorCode, 'Invalid input', $detail, $pointer);
    }
}
