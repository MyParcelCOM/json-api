<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Http;

use MyParcelCom\JsonApi\Exceptions\PaginatorException;
use MyParcelCom\JsonApi\Http\Paginator;
use PHPUnit\Framework\TestCase;

class PaginatorTest extends TestCase
{
    protected Paginator $paginator;

    protected int $perPage;

    protected int $total;

    protected int $curPage;

    protected int $start;

    protected int $end;

    protected function setUp(): void
    {
        parent::setUp();

        // Expected default values
        $this->curPage = 1;
        $this->perPage = 5;
        $this->total = 15;
        $this->end = (int) ceil($this->total / $this->perPage);
        $this->start = (int) (($this->curPage - 1) * $this->perPage);

        $this->paginator = new Paginator('https://link', $this->perPage, $this->curPage, $this->total);
    }

    public function testGetLinks(): void
    {
        $this->paginator->setBaseUrl('https://foo');
        $lastPage = ceil($this->total / $this->perPage);
        $links = [
            'self'  => 'https://foo?page[size]=' . $this->perPage . '&page[number]=' . $this->curPage,
            'first' => 'https://foo?page[size]=' . $this->perPage . '&page[number]=1',
            'last'  => 'https://foo?page[size]=' . $this->perPage . '&page[number]=' . $lastPage,
            'next'  => 'https://foo?page[size]=' . $this->perPage . '&page[number]=' . ($this->curPage + 1),
        ];

        $this->assertEquals($links, $this->paginator->getLinks());

        $this->paginator->setCurrentPage(2);
        $links = [
            'self'  => 'https://foo?page[size]=' . $this->perPage . '&page[number]=2',
            'first' => 'https://foo?page[size]=' . $this->perPage . '&page[number]=1',
            'last'  => 'https://foo?page[size]=' . $this->perPage . '&page[number]=' . $lastPage,
            'next'  => 'https://foo?page[size]=' . $this->perPage . '&page[number]=3',
            'prev'  => 'https://foo?page[size]=' . $this->perPage . '&page[number]=1',
        ];

        $this->assertEquals($links, $this->paginator->getLinks());
    }

    public function testCurrentPage(): void
    {
        $this->assertEquals($this->curPage, $this->paginator->getCurrentPage());
        $this->paginator->setCurrentPage(4);
        $this->assertEquals(4, $this->paginator->getCurrentPage());

        $this->paginator->setCurrentPage(0);
        $this->assertEquals(1, $this->paginator->getCurrentPage());

        $this->paginator->setCurrentPage(-3);
        $this->assertEquals(1, $this->paginator->getCurrentPage());
    }

    public function testPerPage(): void
    {
        $this->assertEquals($this->perPage, $this->paginator->getPerPage());
        $this->paginator->setPerPage(4);
        $this->assertEquals(4, $this->paginator->getPerPage());

        $this->paginator->setPerPage(-9001);
        $this->assertEquals(Paginator::DEFAULT_PAGE_SIZE, $this->paginator->getPerPage());

        $this->paginator->setMaxPageSize(137);
        $this->assertEquals(137, $this->paginator->getPerPage());
    }

    public function testTotal(): void
    {
        $this->paginator->setTotal(4);
        $this->assertEquals(4, $this->paginator->getTotal());
        $this->paginator->addTotal(4);
        $this->assertEquals(8, $this->paginator->getTotal());

        $this->expectException(PaginatorException::class);
        $this->paginator->setTotal(-1337);
    }

    public function testGetLastPage(): void
    {
        $this->assertEquals($this->end, $this->paginator->getLastPage());
    }

    public function testMaxPageSize(): void
    {
        $this->paginator->setMaxPageSize(444);
        $this->assertEquals(444, $this->paginator->getMaxPageSize());
    }

    public function testGetCount(): void
    {
        $this->assertEquals($this->end, $this->paginator->getCount());
    }

    public function testGetStart(): void
    {
        $this->assertEquals($this->start, $this->paginator->getStart());
    }

    public function testGetEnd(): void
    {
        $this->assertEquals(($this->start + $this->perPage), $this->paginator->getEnd());
    }
}
