<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Exceptions;

use MyParcelCom\JsonApi\Traits\EnumTrait;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use UnitEnum;

class ResourceNotFoundException extends AbstractException
{
    use EnumTrait;

    public function __construct(UnitEnum|string $resourceType, Throwable $previous = null)
    {
        parent::__construct(
            sprintf('One or more of the %s resource could not be found.', $this->getEnumValue($resourceType)),
            self::RESOURCE_NOT_FOUND,
            Response::HTTP_NOT_FOUND,
            $previous,
        );
    }
}
