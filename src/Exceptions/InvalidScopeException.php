<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Exceptions;

use MyParcelCom\JsonApi\Traits\EnumTrait;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * This exception is thrown when a scope is either not available at all, not unavailable for the chosen grant type or
 * not attached to the requesting client.
 */
class InvalidScopeException extends AbstractException
{
    use EnumTrait;

    public function __construct(array $scopeSlugs, Throwable $previous = null)
    {
        $scopeStrings = collect($scopeSlugs)
            ->map(fn (mixed $scopeSlug) => $this->getEnumValue($scopeSlug))
            ->toArray();

        parent::__construct(
            'The following scopes are not available to the requesting client: ' . implode(', ', $scopeStrings),
            self::AUTH_INVALID_SCOPE,
            Response::HTTP_FORBIDDEN,
            $previous,
        );
    }
}
