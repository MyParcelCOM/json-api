<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class MissingBillingInformationException extends AbstractException
{
    /**
     * @param string          $detail
     * @param \Throwable|null $previous
     */
    public function __construct(string $detail, \Throwable $previous = null)
    {
        parent::__construct(
            $detail,
            self::MISSING_BILLING_INFORMATION,
            Response::HTTP_PAYMENT_REQUIRED,
            $previous
        );
    }
}
