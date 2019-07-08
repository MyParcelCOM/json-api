<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Exceptions;

use Symfony\Component\HttpFoundation\Response;

/**
 * This exception is thrown when a resource cannot be found.
 */
class TooManyRequestsException extends AbstractException
{
    /**
     * @param string          $detail
     * @param \Throwable|null $previous
     */
    public function __construct(string $detail, \Throwable $previous = null)
    {
        parent::__construct(
            $detail,
            self::TOO_MANY_REQUESTS,
            Response::HTTP_TOO_MANY_REQUESTS,
            $previous
        );
    }
}
