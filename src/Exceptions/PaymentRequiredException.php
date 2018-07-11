<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class PaymentRequiredException extends AbstractException
{
    /**
     * @param string          $detail
     * @param \Throwable|null $previous
     */
    public function __construct(string $detail, \Throwable $previous = null)
    {
        parent::__construct(
            $detail,
            self::PAYMENT_REQUIRED,
            Response::HTTP_PAYMENT_REQUIRED,
            $previous
        );
    }
}
