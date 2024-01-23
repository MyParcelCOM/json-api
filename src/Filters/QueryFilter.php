<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Filters;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;
use MyParcelCom\JsonApi\Filters\Interfaces\FilterInterface;

class QueryFilter implements FilterInterface
{
    public function __construct(
        protected Builder $query,
    ) {
    }

    /**
     * Applies a filter to the current query. Filters given column on given value using given operator.
     *
     * @param string|string[] $column
     * @param string          $operator
     * @param mixed           $value
     * @return Builder
     */
    public function apply($column, string $operator, $value): Builder
    {
        [$operator, $values, $columns] = $this->prepareOperatorValueAndColumn($operator, $value, $column);

        if ($values === null) {
            return $this->filterNullValue($columns, $operator);
        }

        if (str_contains($operator, 'like')) {
            return $this->filterUsingLikeOperator($columns, $operator, $values);
        }

        if (is_array($values)) {
            return $this->filterValuesArray($columns, $operator, $values);
        }

        return $this->filterQuery($columns, $operator, $values);
    }

    /**
     * Check if given operator is a negation.
     */
    private function isNegation(string $operator): bool
    {
        return str_contains($operator, '!') || str_contains($operator, 'not');
    }

    /**
     * Normalize the given operator, value and column.
     *
     * @param string          $operator
     * @param mixed           $value
     * @param string|string[] $column
     * @return array
     */
    private function prepareOperatorValueAndColumn(string $operator, $value, $column): array
    {
        $operator = strtolower($operator);

        if (is_array($value) && count($value) === 1) {
            $value = reset($value);
        }

        if (!is_array($column)) {
            $column = [$column];
        }

        if ($value === null) {
            return [$operator, $value, $column];
        }

        if (str_contains($operator, 'like')) {
            if (!is_array($value)) {
                $value = [$value];
            }

            $value = array_map(function ($v) {
                return '%' . strtolower($v) . '%';
            }, $value);
        }

        return [$operator, $value, $column];
    }

    /**
     * Iterate over the values array and search for the values in each column using the like operator.
     */
    private function filterUsingLikeOperator(array $columns, string $operator, array $values): Builder
    {
        return $this->query->where(function (Builder $query) use ($columns, $values, $operator) {
            array_walk($values, function ($v) use ($columns, $query, $operator) {
                array_walk($columns, function ($columnName) use ($query, $operator, $v) {
                    $query->orWhere(
                        new Expression('lower(' . $columnName . ')'),
                        $operator,
                        $v,
                    );
                });
            });
        });
    }

    /**
     * Filter for value is or is not null.
     */
    private function filterNullValue(array $columns, string $operator): Builder
    {
        return $this->query->where(function (Builder $query) use ($columns, $operator) {
            array_walk($columns, function ($columnName) use ($query, $operator) {
                if ($this->isNegation($operator)) {
                    $query->orWhereNotNull($columnName);

                    return;
                }

                $query->orWhereNull($columnName);
            });
        });
    }

    /**
     * Filter for when values is an array.
     */
    private function filterValuesArray(array $columns, string $operator, array $values): Builder
    {
        return $this->query->where(function (Builder $query) use ($columns, $operator, $values) {
            $where = 'orWhere' . ($this->isNegation($operator) ? 'Not' : '') . 'In';

            array_walk($columns, function ($columnName) use ($query, $operator, $values, $where) {
                $query->$where($columnName, $values);
            });
        });
    }

    /**
     * Filter the query for the given values in the given columns.
     */
    public function filterQuery(array $columns, string $operator, string $values): Builder
    {
        return $this->query->where(function (Builder $query) use ($columns, $operator, $values) {
            array_walk($columns, function ($columnName) use ($query, $operator, $values) {
                $query->orWhere($columnName, $operator, $values);
            });
        });
    }
}
