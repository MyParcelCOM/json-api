<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ResourceHandledBy3rdPartyException extends AbstractException
{
    public function __construct(string $resourceType, string $platform, Throwable $previous = null)
    {
        $message = sprintf('One or more of the %s resource is handled by a 3rd party.', $resourceType);

        parent::__construct(
            $message,
            self::RESOURCE_HANDLED_BY_3RD_PARTY,
            Response::HTTP_CONFLICT,
            $previous,
        );

        $this->setMeta(['3rd_party' => $platform]);
    }


}
