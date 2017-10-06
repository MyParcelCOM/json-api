<?php declare(strict_types=1);

namespace MyParcelCom\Exceptions;

/**
 * Thrown when a requests to an external service fails.
 */
class ExternalRequestException extends AbstractJsonApiException
{
    /**
     * @param int             $status
     * @param int             $externalStatus
     * @param array           $externalError
     * @param \Throwable|null $previous
     */
    public function __construct(int $status, int $externalStatus, array $externalError = [], \Throwable $previous = null)
    {
        parent::__construct(
            "An error occurred while making a request to an external service. When available, details can be found in the meta of this request. If the problem persists, please contact support.",
            JsonApiExceptionInterface::EXTERNAL_REQUEST_ERROR,
            $status,
            $previous
        );

        $this->addMeta('external_status', $externalStatus);

        if (count($externalError)) {
            $this->addMeta('external_error', $externalError);
        }
    }
}
