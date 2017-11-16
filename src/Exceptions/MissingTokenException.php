<?php declare(strict_types=1);

namespace MyParcelCom\Exceptions;

class MissingTokenException extends AbstractJsonApiException
{
    public function __construct(\Throwable $previous = null)
    {
        parent::__construct(
            'No access token was provided for the request. Please add this to the \'Authorization: Bearer\' header.',
            JsonApiExceptionInterface::AUTH_MISSING_TOKEN,
            401,
            $previous
        );
    }
}
