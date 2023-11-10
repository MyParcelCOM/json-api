<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Errors;

/**
 * Created when an error is returned by the carrier, but we are unable to map it to a specific error.
 */
class GenericCarrierError extends AbstractCarrierError
{
    public function __construct(string $errorCode, string $detail, string $pointer = null)
    {
        parent::__construct($errorCode, 'Generic carrier error', $detail, $pointer);
    }
}
