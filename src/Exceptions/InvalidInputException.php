<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Exceptions;

use Throwable;

/**
 * Thrown when carrier response contains errors
 * regarding given input.
 */
class InvalidInputException extends AbstractMultiErrorException
{
    /**
     * @param array          $errors
     * @param int            $status
     * @param Throwable|null $previous
     */
    public function __construct(array $errors, int $status = 422, ?Throwable $previous = null)
    {
        parent::__construct($errors, $status, $previous);
    }
}
