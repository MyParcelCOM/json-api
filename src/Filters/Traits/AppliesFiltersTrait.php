<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Filters\Traits;

use Illuminate\Database\Eloquent\Builder;
use MyParcelCom\JsonApi\Filters\QueryFilter;

trait AppliesFiltersTrait
{
    /**
     * Applies filters to eloquent query builder. Requires a filters property on current class in the following format:
     * $filters = [
     *     '<filter_name>' => [
     *         'column'   => '<column_name>',
     *         'operator' => '<operator>',
     *     ],
     * ];
     *
     * @param array   $filters
     * @param Builder $query
     * @return Builder
     */
    protected function applyFiltersToQuery(array $filters, Builder $query)
    {
        $filter = new QueryFilter($query->getQuery());

        array_walk($filters, function ($value, $name) use ($filter) {
            if (!isset($this->filters[$name])) {
                return;
            }

            $values = array_filter(explode(',', $value));

            $filter->apply(
                $this->filters[$name]['column'] ?? $name,
                $this->filters[$name]['operator'] ?? '=',
                $values
            );
        });

        return $query;
    }
}
