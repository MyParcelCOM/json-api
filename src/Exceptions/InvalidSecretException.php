<?php declare(strict_types=1);

namespace MyParcelCom\Exceptions;

/**
 * Thrown when a request to an external carrier api does not contain a valid secret.
 */
class InvalidSecretException extends AbstractJsonApiException
{
    public function __construct(\Throwable $previous = null)
    {
        parent::__construct(
            'The provided secret is invalid. Please try again later. If the problem persists, contact customer support.',
            JsonApiExceptionInterface::INVALID_SECRET,
            401,
            $previous
        );
    }
}
