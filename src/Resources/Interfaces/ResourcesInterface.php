<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Resources\Interfaces;

use Illuminate\Support\Collection;

interface ResourcesInterface
{
    /**
     * Get the data from the result set as a collection, starting at set offset with a length of given limit.
     */
    public function get(): Collection;

    /**
     * Get the total number of elements
     */
    public function count(): int;

    public function offset(int $offset): self;

    public function limit(int $limit): self;
}
