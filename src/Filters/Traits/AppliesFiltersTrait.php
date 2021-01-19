<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Filters\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
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
        $filters = Arr::dot($filters);
        $queryFilter = new QueryFilter($query->getQuery());

        array_walk($filters, function ($value, $name) use ($queryFilter) {
            if (!isset($this->filters[$name])) {
                return;
            }

            // Append time to date, to query with <= and >= like Elasticsearch, instead of using DB::raw('DATE(column)')
            if (strpos($name, 'date_from') !== false ||
                strpos($name, 'date_to') !== false
            ) {
                if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                    throw new UnprocessableEntityException('The filter ' . $name . ' is not in ISO 8601 date format');
                }
                $value .= (strpos($name, 'date_to') !== false) ? ' 23:59:59' : ' 00:00:00';
            }

            $values = array_filter(explode(',', $value));

            $queryFilter->apply(
                $this->filters[$name]['column'] ?? $name,
                $this->filters[$name]['operator'] ?? '=',
                $values
            );
        });

        return $query;
    }
}
