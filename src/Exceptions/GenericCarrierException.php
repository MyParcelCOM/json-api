<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Exceptions;

use Throwable;

/**
 * Thrown when carrier returned errors that
 * we could not map. This is the fallback exception.
 */
class GenericCarrierException extends AbstractMultiErrorException
{
    /**
     * @param array          $errors
     * @param int            $status
     * @param Throwable|null $previous
     */
    public function __construct(array $errors, int $status = 500, ?Throwable $previous = null)
    {
        parent::__construct($errors, $status, $previous);
    }
}
