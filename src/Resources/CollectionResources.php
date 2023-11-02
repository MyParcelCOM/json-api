<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Resources;

use Illuminate\Support\Collection;
use MyParcelCom\JsonApi\Http\Paginator;
use MyParcelCom\JsonApi\Resources\Interfaces\ResourcesInterface;

class CollectionResources implements ResourcesInterface
{
    protected int $limit = Paginator::DEFAULT_PAGE_SIZE;

    protected int $offset = 0;

    public function __construct(
        protected Collection $collection,
    ) {
    }

    /**
     * Get the data from the collection, starting at set offset with a length of given limit.
     */
    public function get(): Collection
    {
        return $this->collection->slice($this->offset, $this->limit);
    }

    /**
     * Get the total number of elements
     */
    public function count(): int
    {
        return count($this->collection);
    }

    public function offset(int $offset): ResourcesInterface
    {
        $this->offset = $offset;

        return $this;
    }

    public function limit(int $limit): ResourcesInterface
    {
        $this->limit = $limit;

        return $this;
    }
}
