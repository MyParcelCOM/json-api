<?php declare(strict_types=1);

namespace MyParcelCom\Exceptions;

class MissingScopeException extends AbstractJsonApiException
{
    public function __construct(array $scopes, \Throwable $previous = null)
    {
        parent::__construct(
            'The used access token does not contain the required scope(s): ' . implode(', ', $scopes) . '.',
            JsonApiExceptionInterface::AUTH_MISSING_SCOPE,
            403,
            $previous
        );
    }
}
