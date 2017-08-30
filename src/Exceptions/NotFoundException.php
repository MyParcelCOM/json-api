<?php declare(strict_types=1);

namespace MyParcelCom\Exceptions;

use Symfony\Component\HttpFoundation\Response;

/**
 * This exception is thrown when a resource cannot be found.
 */
class NotFoundException extends AbstractJsonApiException
{
    /**
     * @param string          $detail
     * @param \Throwable|null $previous
     */
    public function __construct(string $detail, \Throwable $previous = null)
    {
        parent::__construct(
            $detail,
            JsonApiExceptionInterface::NOT_FOUND,
            Response::HTTP_NOT_FOUND,
            $previous
        );
    }
}
