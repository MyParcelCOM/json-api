<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Mocks\Filters;

use MyParcelCom\JsonApi\Filters\Traits\AppliesFiltersTrait;

class AppliesFiltersMock
{
    use AppliesFiltersTrait;

    private $filters = [
        'date_from' => [
            'column'   => 'created_at',
            'operator' => '>=',
        ],
        'coffee'    => [
            'column'   => 'sugar',
            'operator' => 'nope',
        ],
    ];

    public function applyFilters($filters, $query)
    {
        return $this->applyFiltersToQuery($filters, $query);
    }
}
