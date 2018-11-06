<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Errors;

/**
 * Created when an error is returned by the carrier,
 * that reflects that the requested data can not be found.
 * For instance when requesting tracking information for a shipment.
 */
class CarrierDataNotFoundError extends AbstractCarrierError
{
    /**
     * CarrierDataNotFoundError constructor.
     *
     * @param string      $errorCode
     * @param string      $detail
     */
    public function __construct(string $errorCode, string $detail)
    {
        parent::__construct($errorCode, 'Carrier data not found', $detail);
    }
}
