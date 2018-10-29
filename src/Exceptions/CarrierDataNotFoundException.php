<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Exceptions;

use Throwable;

/**
 * Thrown when the requested data was not found by the carrier.
 */
class CarrierDataNotFoundException extends AbstractMultiErrorException
{
    /**
     * @param array          $errors
     * @param int            $status
     * @param Throwable|null $previous
     */
    public function __construct(array $errors, int $status = 404, ?Throwable $previous = null)
    {
        parent::__construct($errors, $status, $previous);
    }
}
