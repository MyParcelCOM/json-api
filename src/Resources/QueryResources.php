<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Resources;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use MyParcelCom\JsonApi\Resources\Interfaces\ResourcesInterface;

class QueryResources implements ResourcesInterface
{
    protected ?int $count = null;

    /** @var callable[] */
    protected array $eachCallbacks = [];

    public function __construct(
        protected Builder $builder,
    ) {
    }

    /**
     * Skip n amount of records.
     */
    public function offset(int $offset): ResourcesInterface
    {
        $this->builder->skip($offset);

        return $this;
    }

    /**
     * Take n amount of records.
     */
    public function limit(int $limit): ResourcesInterface
    {
        $this->builder->take($limit);

        return $this;
    }

    /**
     * Get the result set.
     */
    public function get(): Collection
    {
        $collection = $this->builder->get(['*']);

        array_walk($this->eachCallbacks, function (callable $callback) use ($collection) {
            $collection->each($callback);
        });

        return $collection;
    }

    /**
     * Get the first or the result set.
     */
    public function first(): ?Model
    {
        return $this->builder->first();
    }

    /**
     * Get the ids or the result set.
     */
    public function getIds(): array
    {
        return $this->builder->get(['id'])->getQueueableIds();
    }

    /**
     * Get the amount of existing records.
     */
    public function count(): int
    {
        if ($this->count === null) {
            $this->count = $this->builder->toBase()->getCountForPagination();
        }

        return $this->count;
    }

    /**
     * Returns a copy of the current query builder.
     */
    public function getQuery(): Builder
    {
        return clone $this->builder;
    }

    /**
     * Apply given callback to each item in the collection.
     *
     * @note For performance purposes, these callbacks will be executed when
     *       `get()` is called.
     */
    public function each(callable $callback): self
    {
        $this->eachCallbacks[] = $callback;

        return $this;
    }
}
