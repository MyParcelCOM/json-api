<?php declare(strict_types=1);

namespace MyParcelCom\Common\ResultSets;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Support\Collection;
use MyParcelCom\Common\Contracts\ResultSetInterface;

class PromiseResultSet implements ResultSetInterface
{
    /** @var PromiseInterface */
    protected $promise;
    /** @var Collection */
    protected $data;
    /** @var int */
    protected $offset = 0;
    /** @var int */
    protected $limit = 30;

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
            $this->data = $this->promise->wait();
        }
    }

    /**
     * @param int $offset
     * @return $this
     */
    public function offset(int $offset): self
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * @param int $limit
     * @return $this
     */
    public function limit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }
}
