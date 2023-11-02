<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * This exception is thrown when a resource cannot be found.
 */
class NotFoundException extends AbstractException
{
    public function __construct(string $detail, Throwable $previous = null)
    {
        parent::__construct(
            $detail,
            self::NOT_FOUND,
            Response::HTTP_NOT_FOUND,
            $previous
        );
    }
}
