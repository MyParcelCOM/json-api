<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Errors;

/**
 * Created when an error is returned by the carrier,
 * that reflects that not necessarily a specific input
 * value is invalid, but the value is invalid for the
 * context it's in. For instance when given service is
 * not supported for the given destination.
 */
class InvalidInputContextError extends AbstractCarrierError
{
    /**
     * @param string      $errorCode
     * @param string      $detail
     * @param string|null $pointer
     */
    public function __construct(string $errorCode, string $detail, string $pointer = null)
    {
        parent::__construct($errorCode, 'Invalid input context', $detail, $pointer);
    }
}
