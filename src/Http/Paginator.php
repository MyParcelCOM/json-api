<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Http;

use MyParcelCom\JsonApi\Exceptions\PaginatorException;

class Paginator
{
    const DEFAULT_PAGE_SIZE = 100;

    /** @var int */
    protected $total;

    /** @var string */
    protected $baseUrl;

    /** @var int */
    protected $perPage;

    /** @var int */
    protected $currentPage;

    /** @var int */
    protected $maxPageSize = self::DEFAULT_PAGE_SIZE;

    public function __construct(string $url = '', int $perPage = self::DEFAULT_PAGE_SIZE, int $currentPage = 1, int $total = 0)
    {
        $this->setBaseUrl($url);
        $this->setPerPage($perPage);
        $this->setCurrentPage($currentPage);
        $this->setTotal($total);
    }

    /**
     * Get the links for other pages in this pagination.
     *
     * @return array
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

    /**
     * Set the current page number.
     *
     * @param int $currentPage
     * @return $this
     * @throws PaginatorException
     */
    public function setCurrentPage(int $currentPage): self
    {
        $this->currentPage = max(1, $currentPage);

        return $this;
    }

    /**
     * Get the current page.
     *
     * @return int
     */
    public function getCurrentPage(): int
    {
        return (int) $this->currentPage;
    }

    /**
     * @return int
     */
    public function getMaxPageSize(): int
    {
        return (int) $this->maxPageSize;
    }

    /**
     * @param int $maxPageSize
     * @return Paginator
     */
    public function setMaxPageSize(int $maxPageSize): self
    {
        $this->maxPageSize = $maxPageSize;

        return $this;
    }

    /**
     * Set the amount we want to retrieve per page
     *
     * @param int $perPage
     * @return $this
     * @throws PaginatorException
     */
    public function setPerPage(int $perPage): self
    {
        $this->perPage = $perPage;

        return $this;
    }

    /**
     * Get the number per page.
     *
     * @return int
     */
    public function getPerPage(): int
    {
        if ($this->perPage < 1 || $this->perPage > $this->getMaxPageSize()) {
            return $this->getMaxPageSize();
        }

        return $this->perPage;
    }

    /**
     * Set the total.
     *
     * @param int $total
     * @return $this
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
     *
     * @param int $total
     * @return $this
     */
    public function addTotal(int $total): self
    {
        $this->total += $total;

        return $this;
    }

    /**
     * Get the total.
     *
     * @return int
     */
    public function getTotal(): int
    {
        return (int) $this->total;
    }

    /**
     * Get the last page.
     *
     * @return int
     */
    public function getLastPage(): int
    {
        return (int) ceil($this->getTotal() / $this->getPerPage());
    }

    /**
     * Get the amount of pages.
     *
     * @return int
     */
    public function getCount(): int
    {
        return (int) ceil($this->getTotal() / $this->getPerPage());
    }

    /**
     * Get the index of the first item that needs to be displayed.
     *
     * @return int
     */
    public function getStart(): int
    {
        return (int) (($this->getCurrentPage() - 1) * $this->getPerPage());
    }

    /**
     * Get the index of the last item that needs to be displayed.
     *
     * @return int
     */
    public function getEnd(): int
    {
        return $this->getStart() + $this->getPerPage();
    }

    /**
     * Set the base url for the other page links.
     *
     * @param string $url
     * @return $this
     */
    public function setBaseUrl(string $url): self
    {
        $this->baseUrl = $url;

        return $this;
    }

    /**
     * Get the url for the given page.
     *
     * @param int $page
     * @return string
     */
    protected function getUrl(int $page): string
    {
        $urlBuilder = new UrlBuilder($this->baseUrl);
        $urlBuilder->addQuery(['page' => ['size' => $this->getPerPage(), 'number' => $page]]);

        return $urlBuilder->getUrl();
    }
}
