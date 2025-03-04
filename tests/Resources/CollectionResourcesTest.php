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
            new Collection(['some', 'random', 'data']),
        );
    }

    public function testGet(): void
    {
        $this->assertInstanceOf(Collection::class, $this->resultSet->get());
        $this->assertEquals(['some', 'random', 'data'], $this->resultSet->get()->toArray());
    }

    public function testCount(): void
    {
        $this->assertEquals(3, $this->resultSet->count());
    }

    public function testOffset(): void
    {
        $this->resultSet->offset(1);

        $this->assertEquals(3, $this->resultSet->count(), 'Offset should not influence the count');
        $this->assertEquals(['random', 'data'], array_values($this->resultSet->get()->toArray()));
    }

    public function testLimit(): void
    {
        $this->resultSet->limit(1);

        $this->assertEquals(3, $this->resultSet->count(), 'Limit should not influence the count');
        $this->assertEquals(['some'], $this->resultSet->get()->toArray());
    }
}
