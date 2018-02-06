<?php declare(strict_types=1);

namespace MyParcelCom\JsonApi\Http;

use MyParcelCom\JsonApi\Exceptions\PaginatorException;

class Paginator
{
    protected $total;
    protected $baseUrl;
    protected $perPage;
    protected $currentPage;
    protected $urlGenerator;
    protected $pageName = "page[number]";

    const DEFAULT_PAGE_SIZE = 30;
    const MAX_PAGE_SIZE = 30;

    public function __construct(string $url = '', int $perPage = self::DEFAULT_PAGE_SIZE, int $currentPage = 1, int $total = 0)
    {
        $this->baseUrl = $url;
        $this->perPage = $perPage;
        $this->currentPage = $currentPage;
        $this->total = $total;
    }

    /**
     * Get the links for other pages in this pagination.
     *
     * @return array
     */
    public function getLinks(): array
    {
        $currentPage = (int)$this->getCurrentPage();
        $lastPage = (int)$this->getLastPage();

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
        if ($currentPage < 1) {
            throw new PaginatorException('current page needs to be 1 or higher');
        }
        $this->currentPage = $currentPage;

        return $this;
    }

    /**
     * Get the current page.
     *
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
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
        if ($perPage < 1) {
            throw new PaginatorException('per page needs to be 1 or higher');
        }
        $this->perPage = $perPage;

        return $this;
    }

    /**
     * Get the number per page.
     *
     * @return int
     */
    public function getPerPage()
    {
        return $this->perPage;
    }

    /**
     * Set the total.
     *
     * @param int $total
     * @return $this
     * @throws PaginatorException
     */
    public function setTotal(int $total)
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
    public function addTotal(int $total)
    {
        $this->total += $total;

        return $this;
    }

    /**
     * Get the total.
     *
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Get the last page.
     *
     * @return int
     */
    public function getLastPage()
    {
        return ceil($this->total / $this->perPage);
    }

    /**
     * Get the amount of pages.
     *
     * @return int
     */
    public function getCount()
    {
        return ceil($this->total / $this->perPage);
    }

    /**
     * Get the index of the first item that needs to be displayed.
     *
     * @return int
     */
    public function getStart(): int
    {
        return (int)(($this->currentPage - 1) * $this->perPage);
    }

    /**
     * Get the index of the last item that needs to be displayed.
     *
     * @return int
     */
    public function getEnd(): int
    {
        return $this->getStart() + $this->perPage;
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
    protected function getUrl(int $page)
    {
        $urlBuilder = new UrlBuilder($this->baseUrl);
        $urlBuilder->addQuery(['page' => ['size' => $this->perPage, 'number' => $page]]);

        return $urlBuilder->getUrl();
    }
}
