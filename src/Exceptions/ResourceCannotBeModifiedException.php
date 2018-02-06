<?php declare(strict_types=1);

namespace MyParcelCom\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class ResourceCannotBeModifiedException extends AbstractJsonApiException
{
    public function __construct(string $message, \Throwable $previous = null)
    {
        parent::__construct(
            $message,
            JsonApiExceptionInterface::RESOURCE_CANNOT_BE_MODIFIED,
            Response::HTTP_LOCKED,
            $previous
        );
    }
}
