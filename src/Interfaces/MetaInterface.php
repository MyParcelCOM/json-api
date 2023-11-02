<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Interfaces;

interface MetaInterface
{
    /**
     * Get the meta from this object.
     */
    public function getMeta(): array;
}
