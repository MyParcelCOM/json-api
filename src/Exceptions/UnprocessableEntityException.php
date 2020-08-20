<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * This exception is thrown when the server understands the content type and
 * the syntax is correct but was unable to process the contained instructions.
 */
class UnprocessableEntityException extends AbstractException
{
    /**
     * @param string         $detail
     * @param Throwable|null $previous
     */
    public function __construct(string $detail, Throwable $previous = null)
    {
        parent::__construct(
            $detail,
            self::UNPROCESSABLE_ENTITY,
            Response::HTTP_UNPROCESSABLE_ENTITY,
            $previous
        );
    }
}
