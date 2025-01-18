<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Exceptions;

use BackedEnum;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use UnitEnum;

/**
 * This exception is thrown when a scope is either not available at all, not unavailable for the chosen grant type or
 * not attached to the requesting client.
 */
class InvalidScopeException extends AbstractException
{
    public function __construct(array $scopeSlugs, Throwable $previous = null)
    {
        $scopeStrings = collect($scopeSlugs)
            ->map(fn ($scope) => match (true) {
                $scope instanceof BackedEnum => $scope->value,
                $scope instanceof UnitEnum   => $scope->name,
                default                      => $scope,
            })
            ->toArray();

        parent::__construct(
            'The following scopes are not available to the requesting client: ' . implode(', ', $scopeStrings),
            self::AUTH_INVALID_SCOPE,
            Response::HTTP_FORBIDDEN,
            $previous,
        );
    }
}
