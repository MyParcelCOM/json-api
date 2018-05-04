<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Exceptions;

/**
 * Thrown when a request to an external service returns an improperly formatted error response.
 */
class InvalidExternalErrorException extends AbstractException
{
    /**
     * @param \Throwable|null $previous
     */
    public function __construct(\Throwable $previous = null)
    {
        parent::__construct(
            "An error was thrown during the request to the external source. We cannot provide more information since the returned error was improperly formatted.",
            self::INVALID_ERROR_SCHEMA,
            500,
            $previous
        );

        $this->addLink('about', 'http://jsonapi.org/format/#errors');
    }
}
