<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * This exception is thrown when a token is invalid.
 */
class InvalidAccessTokenException extends AbstractException
{
    public function __construct(string $detail, Throwable $previous = null)
    {
        parent::__construct(
            $detail,
            self::AUTH_INVALID_TOKEN,
            Response::HTTP_UNAUTHORIZED,
            $previous
        );
    }
}
