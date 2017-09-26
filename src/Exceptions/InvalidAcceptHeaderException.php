<?php declare(strict_types=1);

namespace MyParcelCom\Exceptions;

use Symfony\Component\HttpFoundation\Response;

/**
 * This exception is thrown when the accept header is invalid.
 */
class InvalidAcceptHeaderException extends AbstractJsonApiException
{
    /**
     * @param int $status
     */
    public function __construct(int $status = Response::HTTP_NOT_ACCEPTABLE)
    {
        parent::__construct(
            'Invalid Accept header, expected: application/vnd.api+json',
            JsonApiExceptionInterface::CLIENT_REQUEST_ERROR,
            $status
        );
    }
}
