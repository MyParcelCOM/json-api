<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * This exception should be thrown when the requesting user does not have rights to
 * perform a specific action on a requested resource.
 */
class ForbiddenException extends AbstractException
{
    public function __construct(string $message = null, Throwable $previous = null)
    {
        parent::__construct(
            $message ?? 'This user is not allowed to perform this action.',
            self::FORBIDDEN,
            Response::HTTP_FORBIDDEN,
            $previous
        );
    }
}
