<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Exceptions;

use MyParcelCom\JsonApi\Traits\EnumTrait;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class MissingScopeException extends AbstractException
{
    use EnumTrait;

    public function __construct(array $scopeSlugs, Throwable $previous = null)
    {
        $scopeStrings = collect($scopeSlugs)
            ->map(fn (mixed $scopeSlug) => $this->getEnumValue($scopeSlug))
            ->toArray();

        parent::__construct(
            'The used access token does not contain the required scope(s): ' . implode(', ', $scopeStrings) . '.',
            self::AUTH_MISSING_SCOPE,
            Response::HTTP_FORBIDDEN,
            $previous,
        );
    }
}
