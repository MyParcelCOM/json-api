<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Interfaces;

interface MetaInterface
{
    /**
     * Get the meta from this object.
     *
     * @return array
     */
    public function getMeta(): array;
}
