<?php declare(strict_types=1);

namespace MyParcelCom\Common\Contracts;

interface MetaInterface
{
    /**
     * Get the meta from this object.
     *
     * @return array
     */
    public function getMeta(): array;
}
