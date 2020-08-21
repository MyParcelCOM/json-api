<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ResourceNotFoundException extends AbstractException
{
    public function __construct(string $resourceType, Throwable $previous = null)
    {
        $message = sprintf('One or more of the %s resource could not be found.', $resourceType);

        parent::__construct(
            $message,
            self::RESOURCE_NOT_FOUND,
            Response::HTTP_NOT_FOUND,
            $previous
        );
    }
}
