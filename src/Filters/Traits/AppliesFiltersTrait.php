<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Filters\Traits;

use Carbon\Carbon;
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
    protected function applyFiltersToQuery(array $filters, Builder $query): Builder
    {
        $filters = Arr::dot($filters);
        $queryFilter = new QueryFilter($query->getQuery());

        array_walk($filters, function ($value, $name) use ($queryFilter) {
            if (!isset($this->filters[$name])) {
                return;
            }

            // Append time to date, to query with <= and >= like Elasticsearch, instead of using DB::raw('DATE(column)')
            if (strpos($name, 'date_from') !== false || strpos($name, 'date_to') !== false) {
                if (!preg_match('/^(\d{4}-\d{2}-\d{2})|(\d+)$/', $value)) {
                    throw new UnprocessableEntityException('The filter ' . $name . ' is not a timestamp or in ISO 8601 date format.');
                }
                // We can safely assume that a timestamp is used if it's numeric.
                if (is_numeric($value)) {
                    $value = Carbon::createFromTimestamp($value)->format('Y-m-d H:i:s');
                } else {
                    // Otherwise, we have to append a time string to include whole days in the results.
                    $value .= (strpos($name, 'date_to') !== false) ? ' 23:59:59' : ' 00:00:00';
                }
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
