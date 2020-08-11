<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Resources;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Support\Collection;
use MyParcelCom\JsonApi\Http\Paginator;
use MyParcelCom\JsonApi\Resources\Interfaces\ResourcesInterface;

class PromiseResources implements ResourcesInterface
{
    /** @var PromiseInterface */
    protected $promise;

    /** @var Collection */
    protected $data;

    /** @var int */
    protected $offset = 0;

    /** @var int */
    protected $limit = Paginator::DEFAULT_PAGE_SIZE;

    public function __construct(PromiseInterface $promise)
    {
        $this->promise = $promise;
    }

    /**
     * @return Collection
     */
    public function get(): Collection
    {
        $this->wait();

        return $this->data->slice($this->offset, $this->limit);
    }

    /**
     * @return int
     */
    public function count(): int
    {
        $this->wait();

        return $this->data->count();
    }

    /**
     * Wait for the promise to resolve.
     */
    private function wait(): void
    {
        if (!isset($this->data)) {
            $this->data = new Collection(
                $this->promise->wait()
            );
        }
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
