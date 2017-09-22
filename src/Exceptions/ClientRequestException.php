<?php declare(strict_types=1);

namespace MyParcelCom\Exceptions;

/**
 * Thrown when requests to external clients fail.
 */
class ClientRequestException extends AbstractJsonApiException
{
    /**
     * @param string $detail
     * @param null   $previous
     */
    public function __construct(string $detail, $previous = null)
    {
        parent::__construct(
            $detail,
            JsonApiExceptionInterface::CLIENT_REQUEST_ERROR,
            500,
            $previous
        );
    }
}
