<?php declare(strict_types=1);

namespace MyParcelCom\Common\Contracts;

use Illuminate\Support\Collection;

/**
 * Interface ResultSet.
 */
interface ResultSetInterface
{
    /**
     * Get the data from the result set as a collection, starting at set offset
     * with a length of given limit.
     *
     * @return Collection
     */
    public function get(): Collection;

    /**
     * Get the total number of elements
     *
     * @return int
     */
    public function count(): int;

    /**
     * @param int $offset
     * @return $this
     */
    public function offset(int $offset);

    /**
     * @param int $limit
     * @return $this
     */
    public function limit(int $limit);
}
