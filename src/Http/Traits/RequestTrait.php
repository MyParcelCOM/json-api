<?php declare(strict_types=1);

namespace MyParcelCom\JsonApi\Http\Traits;

use MyParcelCom\JsonApi\Http\Paginator;

trait RequestTrait
{
    /**
     * Get the pagination from the current url.
     *
     * @return Paginator
     */
    public function getPaginator(): Paginator
    {
        $page = $this->query('page', []);
        $paginator = new Paginator($this->fullUrl());

        if (isset($page['size'])) {
            $paginator->setPerPage(
                min(Paginator::MAX_PAGE_SIZE, max(1, (int)$page['size']))
            );
        }
        if (isset($page['number'])) {
            $paginator->setCurrentPage(
                max(1, (int)$page['number'])
            );
        }

        return $paginator;
    }

    /**
     * Get the requested includes from the url.
     *
     * @return array
     */
    public function getIncludes(): array
    {
        return explode(',', $this->query('include', ''));
    }

    /**
     * Get the sort from the requested url.
     *
     * @return array
     */
    public function getSort(): array
    {
        $sort = [];
        foreach (explode(',', $this->query('sort', '')) as $param) {
            $sort[str_replace('-', '', $param)] = strpos($param, '-') === 0 ? 'DESC' : 'ASC';
        }

        return $sort;
    }

    /**
     * Get the filters from the requested url.
     *
     * @return array
     */
    public function getFilter(): array
    {
        return $this->query('filter', []);
    }
}
