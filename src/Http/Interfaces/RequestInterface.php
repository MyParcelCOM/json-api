<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Http\Interfaces;

use MyParcelCom\JsonApi\Http\Paginator;

interface RequestInterface
{
    public const CONTENT_TYPE_JSON_API = 'application/vnd.api+json';

    /**
     * Get the pagination from the requested url.
     */
    public function getPaginator(): Paginator;

    /**
     * Get the requested includes from the url.
     */
    public function getIncludes(): array;

    /**
     * Get the sort from the requested url.
     */
    public function getSort(): array;

    /**
     * Get the filters from the requested url.
     */
    public function getFilter(): array;

    /**
     * Retrieve a query string item from the request.
     *
     * @param string|null       $key
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
