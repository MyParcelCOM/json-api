<?php declare(strict_types=1);

namespace MyParcelCom\Exceptions;

use Symfony\Component\HttpFoundation\Response;

/**
 * This exception is thrown when a scope is either not available at all,
 * not unavailable for the chosen grant type or not attached to the
 * requesting client.
 */
class InvalidScopeException extends AbstractJsonApiException
{
    /**
     * @param array           $slugs
     * @param \Throwable|null $previous
     */
    public function __construct(array $slugs, \Throwable $previous = null)
    {
        parent::__construct(
            "The following scopes are not available to the requesting client: " . implode(", ", $slugs),
            JsonApiExceptionInterface::AUTH_INVALID_SCOPE,
            Response::HTTP_FORBIDDEN,
            $previous
        );
    }
}
