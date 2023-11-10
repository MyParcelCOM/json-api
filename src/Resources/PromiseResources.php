<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Resources;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Support\Collection;
use MyParcelCom\JsonApi\Http\Paginator;
use MyParcelCom\JsonApi\Resources\Interfaces\ResourcesInterface;

class PromiseResources implements ResourcesInterface
{
    protected ?Collection $data = null;

    protected int $offset = 0;

    protected int $limit = Paginator::DEFAULT_PAGE_SIZE;

    public function __construct(
        protected PromiseInterface $promise,
    ) {
    }

    public function get(): Collection
    {
        $this->wait();

        return $this->data->slice($this->offset, $this->limit);
    }

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
        if (empty($this->data)) {
            $this->data = new Collection(
                $this->promise->wait()
            );
        }
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
