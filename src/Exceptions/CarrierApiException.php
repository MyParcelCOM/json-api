<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Exceptions;

use Throwable;

/**
 * Thrown when carrier api requests fail.
 */
class CarrierApiException extends AbstractException
{
    public function __construct(int $status, array $carrierApiResponse, Throwable $previous = null)
    {
        parent::__construct(
            'There was a problem with the request to the carrier. The original response can be found in the meta under `carrier_response`.',
            self::CARRIER_API_ERROR,
            $status,
            $previous,
        );

        $this->addMeta('carrier_response', $carrierApiResponse);
        $this->addMeta('carrier_status', $status);
    }
}
