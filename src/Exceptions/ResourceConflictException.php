<?php declare(strict_types=1);

namespace MyParcelCom\JsonApi\Exceptions;

use Symfony\Component\HttpFoundation\Response;

/**
 * This exception is thrown when request data contains a conflicting id or type.
 */
class ResourceConflictException extends AbstractException
{
    /**
     * @param string          $field
     * @param \Throwable|null $previous
     */
    public function __construct(string $field, \Throwable $previous = null)
    {
        parent::__construct(
            "The supplied resource `$field` is invalid.",
            self::RESOURCE_CONFLICT,
            Response::HTTP_CONFLICT,
            $previous
        );
    }
}
