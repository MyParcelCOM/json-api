<?php declare(strict_types=1);

namespace MyParcelCom\Common\Tests\Resources;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Support\Collection;
use Mockery;
use MyParcelCom\Common\Resources\PromiseResources;
use PHPUnit\Framework\TestCase;

class PromiseResourcesTest extends TestCase
{
    public function tearDown()
    {
        parent::tearDown();
        Mockery::close();
    }

    /** @test */
    public function testGet()
    {
        $resultSet = new PromiseResources(
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
        $resultSet = new PromiseResources(
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
        $resultSet = new PromiseResources(
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
        $resultSet = new PromiseResources(
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
}