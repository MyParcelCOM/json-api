<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Filters\Traits;

use Illuminate\Database\Eloquent\Builder;
use MyParcelCom\JsonApi\Exceptions\UnprocessableEntityException;
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

    /**
     * Append time to date filters to avoid using a raw query with DATE(created_at/register_at).
     * @param string $filter
     * @param bool   $lastMinute
     * @param array  $filters
     * @throws UnprocessableEntityException
     */
    private function setupDateFilter(string $filter, bool $lastMinute, array &$filters): void
    {
        if (!empty($filters[$filter])) {
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $filters[$filter])) {
                $filterPath = str_replace('.', '][', $filter);
                throw new UnprocessableEntityException("The filter[${filterPath}] is not in ISO 8601 date format");
            }
            $filters[$filter] .= $lastMinute ? ' 23:59:59' : ' 00:00:00';
        }
    }
}
