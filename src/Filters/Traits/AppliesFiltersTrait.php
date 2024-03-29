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
            if (str_contains($name, 'date_from') || str_contains($name, 'date_to')) {
                // Date string 0000-00-00
                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                    $value .= (str_contains($name, 'date_to')) ? ' 23:59:59' : ' 00:00:00';
                    // ISO 8601 date string 0000-00-00T00:00:00 with optionally .0 to .000000 or Z +/- 00:00 or 0000
                } elseif (preg_match('/^\d{4}-(?:0[1-9]|1[0-2])-(?:[0-2][0-9]|3[0-1])T(?:[0-1][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9](?:\.\d{1,6})?(Z|[+-]\d{2}:?\d{2})$/', $value)) {
                    $value = (new Carbon($value))->utc()->format('Y-m-d H:i:s');
                    // Timestamp
                } elseif (is_numeric($value)) {
                    $value = Carbon::createFromTimestamp($value)->format('Y-m-d H:i:s');
                } else {
                    throw new UnprocessableEntityException(
                        'The filter ' . $name . ' is not a timestamp, date string or in ISO 8601 date format.',
                    );
                }
            }

            $values = array_filter(explode(',', $value));

            $queryFilter->apply(
                $this->filters[$name]['column'] ?? $name,
                $this->filters[$name]['operator'] ?? '=',
                $values,
            );
        });

        return $query;
    }
}
