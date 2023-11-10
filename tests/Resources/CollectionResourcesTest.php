<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Resources;

use Illuminate\Support\Collection;
use MyParcelCom\JsonApi\Resources\CollectionResources;
use PHPUnit\Framework\TestCase;

class CollectionResourcesTest extends TestCase
{
    private CollectionResources $resultSet;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resultSet = new CollectionResources(
            new Collection(['some', 'random', 'data'])
        );
    }

    /** @test */
    public function testGet()
    {
        $this->assertInstanceOf(Collection::class, $this->resultSet->get());
        $this->assertEquals(['some', 'random', 'data'], $this->resultSet->get()->toArray());
    }

    /** @test */
    public function testCount()
    {
        $this->assertEquals(3, $this->resultSet->count());
    }

    /** @test */
    public function testOffset()
    {
        $this->resultSet->offset(1);

        $this->assertEquals(3, $this->resultSet->count(), 'Offset should not influence the count');
        $this->assertEquals(['random', 'data'], array_values($this->resultSet->get()->toArray()));
    }

    /** @test */
    public function testLimit()
    {
        $this->resultSet->limit(1);

        $this->assertEquals(3, $this->resultSet->count(), 'Limit should not influence the count');
        $this->assertEquals(['some'], $this->resultSet->get()->toArray());
    }
}
