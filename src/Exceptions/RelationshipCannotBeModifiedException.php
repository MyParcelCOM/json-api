<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Exceptions;

use MyParcelCom\JsonApi\Traits\EnumTrait;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use UnitEnum;

class RelationshipCannotBeModifiedException extends AbstractException
{
    use EnumTrait;

    public function __construct(UnitEnum|string $relationshipType, Throwable $previous = null)
    {
        parent::__construct(
            sprintf(
                "The relationship of type '%s' cannot be modified on this resource.",
                $this->getEnumValue($relationshipType),
            ),
            self::RELATIONSHIP_CANNOT_BE_MODIFIED,
            Response::HTTP_FORBIDDEN,
            $previous,
        );
    }
}
