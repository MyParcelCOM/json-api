<?php declare(strict_types=1);

namespace MyParcelCom\Common\Resources;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use MyParcelCom\Common\Contracts\ResourcesInterface;


class QueryResources implements ResourcesInterface
{
    /** @var Builder */
    protected $builder;
    /** @var int */
    protected $count;

    /**
     * @param Builder $builder the root query
     */
    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * skip n amount of records
     *
     * @param  int $offset
     * @return ResourcesInterface
     */
    public function offset(int $offset): ResourcesInterface
    {
        $this->builder->skip($offset);

        return $this;
    }

    /**
     * take n amount of records
     *
     * @param  int $limit
     * @return ResourcesInterface
     */
    public function limit(int $limit): ResourcesInterface
    {
        $this->builder->take($limit);

        return $this;
    }

    /**
     * get the result set
     *
     * @return Collection
     */
    public function get(): Collection
    {
        return $this->builder->get(['*']);
    }

    /**
     * get the first or the result set
     *
     * @return Model
     */
    public function first(): Model
    {
        return $this->builder->first();
    }

    /**
     * get the ids or the result set
     *
     * @return array ids
     */
    public function getIds(): array
    {
        return $this->builder->get(['id'])->getQueueableIds();
    }

    /**
     * get the ammount of existing records
     *
     * @return int count
     */
    public function count(): int
    {
        if (!isset($this->count)) {
            $this->count = (int)$this->builder->toBase()->getCountForPagination();
        }

        return $this->count;
    }
}
