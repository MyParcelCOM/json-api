<?php declare(strict_types=1);

namespace MyParcelCom\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class ResourceNotFoundException extends AbstractJsonApiException
{
    public function __construct(string $resourceType, \Throwable $previous = null)
    {
        $message = sprintf('One or more of the %s resource could not be found.', $resourceType);

        parent::__construct(
            $message,
            JsonApiExceptionInterface::RESOURCE_NOT_FOUND,
            Response::HTTP_NOT_FOUND,
            $previous
        );
    }
}
