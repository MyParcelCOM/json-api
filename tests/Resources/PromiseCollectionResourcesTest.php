<?php declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Resources;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Support\Collection;
use Mockery;
use MyParcelCom\JsonApi\Resources\PromiseCollectionResources;
use PHPUnit\Framework\TestCase;

class PromiseCollectionResourcesTest extends TestCase
{
    public function tearDown()
    {
        parent::tearDown();
        Mockery::close();
    }

    /** @test */
    public function testGet()
    {
        $resultSet = new PromiseCollectionResources(
            Mockery::mock(PromiseInterface::class, [
                'wait' => new Collection(['some', 'random', 'data']),
            ])
        );

        $this->assertInstanceOf(
            Collection::class,
            $resultSet->get()
        );

        $this->assertEquals(
            ['some', 'random', 'data'],
            $resultSet->get()->toArray()
        );
    }

    /** @test */
    public function testCount()
    {
        $resultSet = new PromiseCollectionResources(
            Mockery::mock(PromiseInterface::class, [
                'wait' => new Collection(['some', 'random', 'data']),
            ])
        );

        $this->assertEquals(
            3,
            $resultSet->count()
        );
    }

    /** @test */
    public function testOffset()
    {
        $resultSet = new PromiseCollectionResources(
            Mockery::mock(PromiseInterface::class, [
                'wait' => new Collection(['some', 'random', 'data']),
            ])
        );

        $resultSet->offset(1);
        $this->assertEquals(
            3,
            $resultSet->count(),
            'Offset should not influence the count'
        );

        $this->assertEquals(
            ['random', 'data'],
            array_values($resultSet->get()->toArray())
        );
    }

    /** @test */
    public function testLimit()
    {
        $resultSet = new PromiseCollectionResources(
            Mockery::mock(PromiseInterface::class, [
                'wait' => new Collection(['some', 'random', 'data']),
            ])
        );

        $resultSet->limit(1);
        $this->assertEquals(
            3,
            $resultSet->count(),
            'Limit should not influence the count'
        );

        $this->assertEquals(
            ['some'],
            $resultSet->get()->toArray()
        );
    }

    public function testAddPromise()
    {
        $resultSet = new PromiseCollectionResources(
            Mockery::mock(PromiseInterface::class, [
                'wait' => new Collection(['some', 'random', 'data']),
            ])
        );

        $resultSet->addPromise(
            Mockery::mock(PromiseInterface::class, [
                'wait' => new Collection(['more', 'crazy', 'things']),
            ])
        );

        $this->assertEquals(
            6,
            $resultSet->count(),
            'Offset should not influence the count'
        );

        $this->assertEquals(
            ['some', 'random', 'data', 'more', 'crazy', 'things'],
            $resultSet->get()->toArray()
        );

        $resultSet->offset(2);
        $this->assertEquals(
            ['data', 'more', 'crazy', 'things'],
            array_values($resultSet->get()->toArray())
        );

        $resultSet->limit(2);
        $this->assertEquals(
            ['data', 'more'],
            array_values($resultSet->get()->toArray())
        );
    }
}
