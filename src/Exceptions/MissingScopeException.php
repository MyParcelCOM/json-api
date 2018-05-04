<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Exceptions;

class MissingScopeException extends AbstractException
{
    public function __construct(array $scopes, \Throwable $previous = null)
    {
        parent::__construct(
            'The used access token does not contain the required scope(s): ' . implode(', ', $scopes) . '.',
            self::AUTH_MISSING_SCOPE,
            403,
            $previous
        );
    }
}
