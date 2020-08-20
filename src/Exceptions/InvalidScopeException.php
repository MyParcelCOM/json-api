<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * This exception is thrown when a scope is either not available at all,
 * not unavailable for the chosen grant type or not attached to the
 * requesting client.
 */
class InvalidScopeException extends AbstractException
{
    /**
     * @param array           $slugs
     * @param Throwable|null $previous
     */
    public function __construct(array $slugs, Throwable $previous = null)
    {
        parent::__construct(
            "The following scopes are not available to the requesting client: " . implode(", ", $slugs),
            self::AUTH_INVALID_SCOPE,
            Response::HTTP_FORBIDDEN,
            $previous
        );
    }
}
