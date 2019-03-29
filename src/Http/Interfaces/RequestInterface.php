<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Http\Interfaces;

use MyParcelCom\JsonApi\Http\Paginator;

interface RequestInterface
{
    /**
     * Get the pagination from the requested url.
     *
     * @return Paginator
     */
    public function getPaginator(): Paginator;

    /**
     * Get the requested includes from the url.
     *
     * @return array
     */
    public function getIncludes(): array;

    /**
     * Get the sort from the requested url.
     *
     * @return array
     */
    public function getSort(): array;

    /**
     * Get the filters from the requested url.
     *
     * @return array
     */
    public function getFilter(): array;

    /**
     * Retrieve a query string item from the request.
     *
     * @param string            $key
     * @param string|array|null $default
     * @return string|array
     */
    public function query($key = null, $default = null);

    /**
     * Get the full URL for the request.
     *
     * @return string
     */
    public function fullUrl();
}
