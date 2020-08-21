<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Errors;

/**
 * Created when an error is returned by the carrier, that reflects that some input is missing.
 * For instance when a postal code is required, but not given.
 */
class MissingInputError extends AbstractCarrierError
{
    /**
     * @param string      $errorCode
     * @param string      $detail
     * @param string|null $pointer
     */
    public function __construct(string $errorCode, string $detail, string $pointer = null)
    {
        parent::__construct($errorCode, 'Missing input', $detail, $pointer);
    }
}
