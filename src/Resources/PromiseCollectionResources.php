<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Resources;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Support\Collection;
use MyParcelCom\JsonApi\Resources\Interfaces\ResourcesInterface;
use function GuzzleHttp\Promise\unwrap;

class PromiseCollectionResources implements ResourcesInterface
{
    /** @var PromiseInterface[] */
    protected $promises = [];

    /** @var array */
    protected $data;

    /** @var int */
    protected $limit;

    /** @var int */
    protected $offset;

    /**
     * @param PromiseInterface[] $promises
     */
    public function __construct(PromiseInterface ...$promises)
    {
        $this->promises = $promises;
    }

    /**
     * Get the data from the result set as a collection, starting at set offset with a length of given limit.
     *
     * @return Collection
     */
    public function get(): Collection
    {
        $this->wait();

        $collection = new Collection();
        foreach ($this->data as $data) {
            $collection = $collection->merge($data);
        }

        return $collection->slice($this->offset, $this->limit);
    }

    /**
     * Get the total number of elements
     *
     * @return int
     */
    public function count(): int
    {
        $this->wait();

        $count = 0;
        foreach ($this->data as $data) {
            $count += count($data);
        }

        return $count;
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

    /**
     * @param PromiseInterface $promise
     * @return $this
     */
    public function addPromise(PromiseInterface $promise): self
    {
        $this->promises[] = $promise;

        return $this;
    }

    /**
     * Wait for the promises to resolve.
     *
     * @return $this
     */
    protected function wait()
    {
        if (!isset($this->data)) {
            $this->data = unwrap($this->promises);
        }

        return $this;
    }
}
