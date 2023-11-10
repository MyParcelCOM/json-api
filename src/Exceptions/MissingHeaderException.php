<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

class MissingHeaderException extends AbstractException
{
    public function __construct(array $headers, Throwable $previous = null)
    {
        parent::__construct(
            'The request does not contain the required header(s): ' . implode(', ', $headers) . '.',
            self::MISSING_REQUEST_HEADER,
            Response::HTTP_FORBIDDEN,
            $previous
        );
    }
}
