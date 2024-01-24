<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ResourceCannotBeModifiedException extends AbstractException
{
    public function __construct(string $message, Throwable $previous = null)
    {
        parent::__construct(
            $message,
            self::RESOURCE_CANNOT_BE_MODIFIED,
            Response::HTTP_LOCKED,
            $previous,
        );
    }
}
