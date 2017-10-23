<?php declare(strict_types=1);

namespace MyParcelCom\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * This exception is thrown when request data contains a conflicting id or type.
 */
class ResourceConflictException extends AbstractJsonApiException
{
    /**
     * @param string         $field
     * @param Throwable|null $previous
     */
    public function __construct(string $field, Throwable $previous = null)
    {
        parent::__construct(
            "The supplied resource `$field` is invalid.",
            JsonApiExceptionInterface::RESOURCE_CONFLICT,
            Response::HTTP_CONFLICT,
            $previous
        );
    }
}
