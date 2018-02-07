<?php declare(strict_types=1);

namespace MyParcelCom\JsonApi\Exceptions;

/**
 * Thrown when carrier api requests fail.
 */
class CarrierApiException extends AbstractException
{
    /**
     * @param int             $status
     * @param array           $carrierApiResponse
     * @param \Throwable|null $previous
     */
    public function __construct(int $status, array $carrierApiResponse, \Throwable $previous = null)
    {
        parent::__construct(
            'There was a problem with the request to the carrier. The original response can be found in the meta under `carrier_response`.',
            self::CARRIER_API_ERROR,
            $status,
            $previous
        );

        $this->addMeta('carrier_response', $carrierApiResponse);
    }
}
