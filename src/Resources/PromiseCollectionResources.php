<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Resources;

use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Promise\Utils;
use Illuminate\Support\Collection;
use MyParcelCom\JsonApi\Http\Paginator;
use MyParcelCom\JsonApi\Resources\Interfaces\ResourcesInterface;

class PromiseCollectionResources implements ResourcesInterface
{
    /** @var PromiseInterface[] */
    protected array $promises = [];

    protected array $data = [];

    protected int $limit = Paginator::DEFAULT_PAGE_SIZE;

    protected int $offset = 0;

    public function __construct(PromiseInterface ...$promises)
    {
        $this->promises = $promises;
    }

    /**
     * Get the data from the result set as a collection, starting at set offset with a length of given limit.
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

    public function addPromise(PromiseInterface $promise): self
    {
        $this->promises[] = $promise;

        return $this;
    }

    protected function wait(): self
    {
        if (empty($this->data)) {
            $this->data = Utils::unwrap($this->promises);
        }

        return $this;
    }
}
