<?php declare(strict_types=1);

namespace MyParcelCom\JsonApi\Exceptions;

class MissingTokenException extends AbstractException
{
    public function __construct(\Throwable $previous = null)
    {
        parent::__construct(
            'No access token was provided for the request. Please add this to the \'Authorization: Bearer\' header.',
            self::AUTH_MISSING_TOKEN,
            401,
            $previous
        );
    }
}
