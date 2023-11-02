<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Http;

use MyParcelCom\JsonApi\Exceptions\PaginatorException;

class Paginator
{
    const DEFAULT_PAGE_SIZE = 100;

    protected int $total;

    protected int $currentPage;

    protected int $maxPageSize = self::DEFAULT_PAGE_SIZE;

    public function __construct(
        protected string $baseUrl = '',
        protected int $perPage = self::DEFAULT_PAGE_SIZE,
        int $currentPage = 1,
        int $total = 0,
    ) {
        $this->setCurrentPage($currentPage);
        $this->setTotal($total);
    }

    /**
     * Get the links for other pages in this pagination.
     */
    public function getLinks(): array
    {
        $currentPage = $this->getCurrentPage();
        $lastPage = $this->getLastPage();

        $pagination = [];

        $pagination['self'] = $this->getUrl($currentPage);
        $pagination['first'] = $this->getUrl(1);

        if ($currentPage > 1) {
            $pagination['prev'] = $this->getUrl($currentPage - 1);
        }

        if ($currentPage < $lastPage) {
            $pagination['next'] = $this->getUrl($currentPage + 1);
        }

        $pagination['last'] = $this->getUrl($lastPage);

        return $pagination;
    }

    public function setCurrentPage(int $currentPage): self
    {
        $this->currentPage = max(1, $currentPage);

        return $this;
    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function getMaxPageSize(): int
    {
        return $this->maxPageSize;
    }

    public function setMaxPageSize(int $maxPageSize): self
    {
        $this->maxPageSize = $maxPageSize;

        return $this;
    }

    /**
     * Set the amount we want to retrieve per page
     */
    public function setPerPage(int $perPage): self
    {
        $this->perPage = $perPage;

        return $this;
    }

    /**
     * Get the number per page.
     */
    public function getPerPage(): int
    {
        if ($this->perPage < 1 || $this->perPage > $this->getMaxPageSize()) {
            return $this->getMaxPageSize();
        }

        return $this->perPage;
    }

    /**
     * @throws PaginatorException
     */
    public function setTotal(int $total): self
    {
        if ($total < 0) {
            throw new PaginatorException('total needs to be 0 or higher');
        }
        $this->total = $total;

        return $this;
    }

    /**
     * Add to the total.
     */
    public function addTotal(int $total): self
    {
        $this->total += $total;

        return $this;
    }

    /**
     * Get the total.
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * Get the last page.
     */
    public function getLastPage(): int
    {
        return (int) ceil($this->getTotal() / $this->getPerPage());
    }

    /**
     * Get the amount of pages.
     */
    public function getCount(): int
    {
        return (int) ceil($this->getTotal() / $this->getPerPage());
    }

    /**
     * Get the index of the first item that needs to be displayed.
     */
    public function getStart(): int
    {
        return (int) (($this->getCurrentPage() - 1) * $this->getPerPage());
    }

    /**
     * Get the index of the last item that needs to be displayed.
     */
    public function getEnd(): int
    {
        return $this->getStart() + $this->getPerPage();
    }

    /**
     * Set the base url for the other page links.
     */
    public function setBaseUrl(string $url): self
    {
        $this->baseUrl = $url;

        return $this;
    }

    /**
     * Get the url for the given page.
     */
    protected function getUrl(int $page): string
    {
        $urlBuilder = new UrlBuilder($this->baseUrl);
        $urlBuilder->addQuery(['page' => ['size' => $this->getPerPage(), 'number' => $page]]);

        return $urlBuilder->getUrl();
    }
}
