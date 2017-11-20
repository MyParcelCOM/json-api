<?php declare(strict_types=1);

namespace MyParcelCom\Common\Contracts;

interface FilterInterface
{
    /**
     * Applies a filter to the set data. Filters the data on set field with
     * given value, using given operator. Returns the filtered data.
     *
     * @param string $field
     * @param string $operator
     * @param mixed $value
     * @return mixed
     */
    public function apply(string $field, string $operator, $value);
}
