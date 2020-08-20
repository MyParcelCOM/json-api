<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Resources;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Support\Collection;
use Mockery;
use MyParcelCom\JsonApi\Resources\PromiseResources;
use PHPUnit\Framework\TestCase;

class PromiseResourcesTest extends TestCase
{
    /** @var PromiseResources */
    private $resultSet;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resultSet = new PromiseResources(
            Mockery::mock(PromiseInterface::class, [
                'wait' => new Collection(['some', 'random', 'data']),
            ])
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        Mockery::close();
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
