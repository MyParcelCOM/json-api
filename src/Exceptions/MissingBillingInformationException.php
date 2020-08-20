<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

class MissingBillingInformationException extends AbstractException
{
    /**
     * @param array          $missingBillingInformation
     * @param Throwable|null $previous
     */
    public function __construct(array $missingBillingInformation, Throwable $previous = null)
    {
        $detail = 'Billing information is incomplete. The following data is missing: ' . implode(', ', $missingBillingInformation);

        parent::__construct(
            $detail,
            self::MISSING_BILLING_INFORMATION,
            Response::HTTP_PAYMENT_REQUIRED,
            $previous
        );
    }
}
