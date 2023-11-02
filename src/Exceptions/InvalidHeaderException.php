<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Exceptions;

use Symfony\Component\HttpFoundation\Response;

/**
 * This exception is thrown when a request header is invalid.
 */
class InvalidHeaderException extends AbstractException
{
    public function __construct(string $detail, int $status = Response::HTTP_NOT_ACCEPTABLE)
    {
        parent::__construct(
            $detail,
            self::INVALID_REQUEST_HEADER,
            $status
        );
    }
}
