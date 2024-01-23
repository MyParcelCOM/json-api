<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Thrown when a request to an external service returns an improperly formatted error response.
 */
class InvalidExternalErrorException extends AbstractException
{
    public function __construct(Throwable $previous = null)
    {
        parent::__construct(
            'An error was thrown during the request to the external source. We cannot provide more information since the returned error was improperly formatted.',
            self::INVALID_ERROR_SCHEMA,
            Response::HTTP_INTERNAL_SERVER_ERROR,
            $previous,
        );

        $this->addLink('about', 'https://jsonapi.org/format/#errors');
    }
}
