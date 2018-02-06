<?php declare(strict_types=1);

namespace MyParcelCom\Common\Resources;

use Illuminate\Support\Collection;
use MyParcelCom\Common\Contracts\ResourcesInterface;

class CollectionResources implements ResourcesInterface
{
    /** @var Collection */
    protected $collection;

    /** @var int */
    protected $limit = 30;

    /** @var int */
    protected $offset = 0;

    /**
     * @param Collection $collection
     */
    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * Get the data from the collection, starting at set offset with a length of given limit.
     *
     * @return Collection
     */
    public function get(): Collection
    {
        return $this->collection->slice($this->offset, $this->limit);
    }

    /**
     * Get the total number of elements
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->collection);
    }

    /**
     * @param int $offset
     * @return ResourcesInterface
     */
    public function offset(int $offset): ResourcesInterface
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * @param int $limit
     * @return ResourcesInterface
     */
    public function limit(int $limit): ResourcesInterface
    {
        $this->limit = $limit;

        return $this;
    }
}
