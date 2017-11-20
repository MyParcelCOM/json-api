<?php declare(strict_types=1);

namespace MyParcelCom\Common\Filters;

use Illuminate\Database\Query\Builder;
use MyParcelCom\Common\Contracts\FilterInterface;

class QueryFilter implements FilterInterface
{
    /** @var Builder */
    protected $query;

    /**
     * @param Builder $query
     */
    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    /**
     * Applies a filter to the current query. Filters given column on given
     * value using given operator.
     *
     * @param string $column
     * @param string $operator
     * @param mixed  $value
     * @return Builder
     */
    public function apply(string $column, string $operator, $value): Builder
    {
        [$operator, $value] = $this->prepareOperatorAndValue($operator, $value);

        if ($value === null) {
            if ($this->isNegation($operator)) {
                return $this->query->whereNotNull($column);
            }

            return $this->query->whereNull($column);
        }

        if (strpos($operator, 'like') !== false) {
            return $this->query->where(function (Builder $query) use ($column, $value, $operator) {
                array_walk($value, function ($v) use ($column, $query, $operator) {
                    $query->where($column, $operator, $v, 'OR');
                });
            });
        }

        if (is_array($value)) {
            $where = 'where' . ($this->isNegation($operator) ? 'Not' : '') . 'In';

            return $this->query->$where($column, $value);
        }

        return $this->query->where($column, $operator, $value);
    }

    /**
     * Check if given operator is a negation.
     *
     * @param string $operator
     * @return bool
     */
    private function isNegation(string $operator)
    {
        return strpos($operator, '!') !== false || strpos($operator, 'not') !== false;
    }

    /**
     * Normalize the given operator and value.
     *
     * @param string $operator
     * @param mixed  $value
     * @return array
     */
    private function prepareOperatorAndValue(string $operator, $value)
    {
        $operator = strtolower($operator);

        if (is_array($value) && count($value) === 1) {
            $value = reset($value);
        }

        if ($value === null) {
            return [$operator, $value];
        }

        if (strpos($operator, 'like') !== false) {
            if (!is_array($value)) {
                $value = [$value];
            }

            $value = array_map(function ($v) {
                return '%' . $v . '%';
            }, $value);
        }

        return [$operator, $value];
    }
}
