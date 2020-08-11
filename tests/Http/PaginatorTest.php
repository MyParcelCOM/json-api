<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Http;

use MyParcelCom\JsonApi\Exceptions\PaginatorException;
use MyParcelCom\JsonApi\Http\Paginator;
use PHPUnit\Framework\TestCase;

class PaginatorTest extends TestCase
{
    /** @var Paginator */
    protected $paginator;

    /** @var int */
    protected $perPage;

    /** @var int */
    protected $total;

    /** @var int */
    protected $curPage;

    /** @var int */
    protected $start;

    /** @var int */
    protected $end;

    protected function setUp()
    {
        parent::setUp();

        // Expected default values
        $this->curPage = 1;
        $this->perPage = 5;
        $this->total = 15;
        $this->end = ceil($this->total / $this->perPage);
        $this->start = (int) (($this->curPage - 1) * $this->perPage);

        $this->paginator = new Paginator('http://link', $this->perPage, $this->curPage, $this->total);
    }

    /** @test */
    public function testGetLinks()
    {
        $this->paginator->setBaseUrl('http://foo');
        $links = [
            'self'  => 'http://foo?page[size]=' . $this->perPage . '&page[number]=' . $this->curPage,
            'first' => 'http://foo?page[size]=' . $this->perPage . '&page[number]=1',
            'last'  => 'http://foo?page[size]=' . $this->perPage . '&page[number]=' . ceil($this->total / $this->perPage),
            'next'  => 'http://foo?page[size]=' . $this->perPage . '&page[number]=' . ($this->curPage + 1),
        ];

        $this->assertEquals($links, $this->paginator->getLinks());

        $this->paginator->setCurrentPage(2);
        $links = [
            'self'  => 'http://foo?page[size]=' . $this->perPage . '&page[number]=2',
            'first' => 'http://foo?page[size]=' . $this->perPage . '&page[number]=1',
            'last'  => 'http://foo?page[size]=' . $this->perPage . '&page[number]=' . ceil($this->total / $this->perPage),
            'next'  => 'http://foo?page[size]=' . $this->perPage . '&page[number]=3',
            'prev'  => 'http://foo?page[size]=' . $this->perPage . '&page[number]=1',
        ];

        $this->assertEquals($links, $this->paginator->getLinks());
    }

    /** @test */
    public function testCurrentPage()
    {
        $this->assertEquals($this->curPage, $this->paginator->getCurrentPage());
        $this->paginator->setCurrentPage(4);
        $this->assertEquals(4, $this->paginator->getCurrentPage());

        $this->paginator->setCurrentPage(0);
        $this->assertEquals(1, $this->paginator->getCurrentPage());

        $this->paginator->setCurrentPage(-3);
        $this->assertEquals(1, $this->paginator->getCurrentPage());
    }

    /** @test */
    public function testPerPage()
    {
        $this->assertEquals($this->perPage, $this->paginator->getPerPage());
        $this->paginator->setPerPage(4);
        $this->assertEquals(4, $this->paginator->getPerPage());

        $this->paginator->setPerPage(-9001);
        $this->assertEquals(30, $this->paginator->getPerPage());

        $this->paginator->setMaxPageSize(70);
        $this->assertEquals(70, $this->paginator->getPerPage());
    }

    /** @test */
    public function testTotal()
    {
        $this->paginator->setTotal(4);
        $this->assertEquals(4, $this->paginator->getTotal());
        $this->paginator->addTotal(4);
        $this->assertEquals(8, $this->paginator->getTotal());

        $this->expectException(PaginatorException::class);
        $this->paginator->setTotal(-1337);
    }

    /** @test */
    public function testGetLastPage()
    {
        $this->assertEquals($this->end, $this->paginator->getLastPage());
    }

    /** @test */
    public function testMaxPageSize()
    {
        $this->paginator->setMaxPageSize(444);
        $this->assertEquals(444, $this->paginator->getMaxPageSize());
    }

    /** @test */
    public function testGetCount()
    {
        $this->assertEquals($this->end, $this->paginator->getCount());
    }

    /** @test */
    public function testGetStart()
    {
        $this->assertEquals($this->start, $this->paginator->getStart());
    }

    /** @test */
    public function testGetEnd()
    {
        $this->assertEquals(($this->start + $this->perPage), $this->paginator->getEnd());
    }
}
