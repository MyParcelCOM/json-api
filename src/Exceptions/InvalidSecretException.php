<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Thrown when a request to an external carrier api does not contain a valid secret.
 */
class InvalidSecretException extends AbstractException
{
    public function __construct(Throwable $previous = null)
    {
        parent::__construct(
            'The provided secret is invalid. Please try again later. If the problem persists, contact customer support.',
            self::INVALID_SECRET,
            Response::HTTP_UNAUTHORIZED,
            $previous,
        );
    }
}
