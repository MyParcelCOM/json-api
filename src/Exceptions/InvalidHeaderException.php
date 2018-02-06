<?php declare(strict_types=1);

namespace MyParcelCom\Exceptions;

use Symfony\Component\HttpFoundation\Response;

/**
 * This exception is thrown when a request header is invalid.
 */
class InvalidHeaderException extends AbstractJsonApiException
{
    /**
     * @param string $detail
     * @param int    $status
     */
    public function __construct(string $detail, int $status = Response::HTTP_NOT_ACCEPTABLE)
    {
        parent::__construct(
            $detail,
            JsonApiExceptionInterface::INVALID_REQUEST_HEADER,
            $status
        );
    }
}
