<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Exceptions;

use BackedEnum;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use UnitEnum;

class MissingScopeException extends AbstractException
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
            'The used access token does not contain the required scope(s): ' . implode(', ', $scopeStrings) . '.',
            self::AUTH_MISSING_SCOPE,
            Response::HTTP_FORBIDDEN,
            $previous,
        );
    }
}
