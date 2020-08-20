<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Errors;

/**
 * Created when an error was returned by the carrier, reflecting that the used credentials were not valid.
 */
class InvalidCredentialsError extends AbstractCarrierError
{
    /**
     * @param string $errorCode
     * @param string $detail
     */
    public function __construct(string $errorCode, string $detail)
    {
        parent::__construct($errorCode, 'Invalid carrier credentials', $detail);
    }
}
