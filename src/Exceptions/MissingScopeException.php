<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

class MissingScopeException extends AbstractException
{
    public function __construct(array $scopes, Throwable $previous = null)
    {
        parent::__construct(
            'The used access token does not contain the required scope(s): ' . implode(', ', $scopes) . '.',
            self::AUTH_MISSING_SCOPE,
            Response::HTTP_FORBIDDEN,
            $previous
        );
    }
}
