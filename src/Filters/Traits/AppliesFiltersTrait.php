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
                // Date string 0000-00-00
                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                    $value .= (strpos($name, 'date_to') !== false) ? ' 23:59:59' : ' 00:00:00';
                    // ISO 8601 date string 0000-00-00T00:00:00+0000
                } elseif (preg_match('/^\d{4}-\d{2}-\d{2}T[0-2][0-9]:[0-5][0-9]:[0-5][0-9]\+\d{4}$/', $value)) {
                    $value = (new Carbon($value))->utc()->format('Y-m-d H:i:s');
                    // Timestamp
                } elseif (is_numeric($value)) {
                    $value = Carbon::createFromTimestamp($value)->format('Y-m-d H:i:s');
                } else {
                    throw new UnprocessableEntityException('The filter ' . $name . ' is not a timestamp, date string or in ISO 8601 date format.');
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
