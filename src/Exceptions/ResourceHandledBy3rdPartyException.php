<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Exceptions;

use MyParcelCom\JsonApi\Traits\EnumTrait;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use UnitEnum;

class ResourceHandledBy3rdPartyException extends AbstractException
{
    use EnumTrait;

    public function __construct(UnitEnum|string $resourceType, string $platform, Throwable $previous = null)
    {
        parent::__construct(
            sprintf('One or more of the %s resource is handled by a 3rd party.', $this->getEnumValue($resourceType)),
            self::RESOURCE_HANDLED_BY_3RD_PARTY,
            Response::HTTP_CONFLICT,
            $previous,
        );

        $this->setMeta(['3rd_party' => $platform]);
    }


}
