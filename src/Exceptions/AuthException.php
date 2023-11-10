<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Exceptions;

use Throwable;

/**
 * This exception is a replacement for the OAuthServerException used by the League package.
 */
class AuthException extends AbstractException
{
    public function __construct(string $detail, int $status, Throwable $previous = null)
    {
        parent::__construct(
            $detail,
            self::AUTH_SERVER_EXCEPTION,
            $status,
            $previous
        );
    }
}
