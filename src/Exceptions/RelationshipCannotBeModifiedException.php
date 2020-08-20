<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

class RelationshipCannotBeModifiedException extends AbstractException
{
    public function __construct(string $relationshipType, Throwable $previous = null)
    {
        parent::__construct(
            "The relationship of type '{$relationshipType}' cannot be modified on this resource.",
            self::RELATIONSHIP_CANNOT_BE_MODIFIED,
            Response::HTTP_FORBIDDEN,
            $previous
        );
    }
}
