<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Http\Traits;

use MyParcelCom\JsonApi\Http\Paginator;
use MyParcelCom\JsonApi\Traits\ArrayFilterTrait;

trait RequestTrait
{
    use ArrayFilterTrait;

    /**
     * Get the pagination from the current url.
     */
    public function getPaginator(): Paginator
    {
        $page = $this->query('page', []);
        $paginator = new Paginator($this->fullUrl());

        if (isset($page['size'])) {
            $paginator->setPerPage((int) $page['size']);
        }
        if (isset($page['number'])) {
            $paginator->setCurrentPage((int) $page['number']);
        }

        return $paginator;
    }

    /**
     * Get the requested includes from the url.
     */
    public function getIncludes(): array
    {
        $includes = [];
        foreach (explode(',', $this->query('include', '')) as $include) {
            $this->addInclude($include, $includes);
        }

        return $includes;
    }

    private function addInclude(string $include, array &$includes): void
    {
        if (!str_contains($include, '.')) {
            $includes[] = $include;

            return;
        }

        $parentInclude = strtok($include, '.');
        if (!isset($includes[$parentInclude])) {
            $includes[$parentInclude] = [];
        }
        $this->addInclude(strtok(""), $includes[$parentInclude]);
    }

    /**
     * Get the sort from the requested url.
     */
    public function getSort(): array
    {
        $sort = [];
        foreach (explode(',', $this->query('sort', '')) as $param) {
            $sort[str_replace('-', '', $param)] = str_starts_with($param, '-') ? 'DESC' : 'ASC';
        }

        return $sort;
    }

    /**
     * Get the filters from the requested url.
     */
    public function getFilter(): array
    {
        return $this->arrayDeepFilter($this->query('filter', []));
    }
}
