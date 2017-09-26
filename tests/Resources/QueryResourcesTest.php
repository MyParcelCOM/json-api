<?php declare(strict_types=1);

namespace MyParcelCom\Common\Tests\Resources;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Mockery;
use MyParcelCom\Common\Resources\QueryResources;
use PHPUnit\Framework\TestCase;

class QueryResourcesTest extends TestCase
{
    /** @var QueryResources */
    private $resultSet;
    /** @var Model[] */
    private $data;
    /** @var int */
    private $skip = 0;
    /** @var int */
    private $take = 30;

    public function setUp()
    {
        parent::setUp();

        $this->data = [
            Mockery::mock(Model::class, ['getId' => 1, 'toArray' => ['id' => 1]]),
            Mockery::mock(Model::class, ['getId' => 2, 'toArray' => ['id' => 2]]),
            Mockery::mock(Model::class, ['getId' => 3, 'toArray' => ['id' => 3]]),
        ];
        $builder = Mockery::mock(Builder::class, [
            'first'                 => $this->data[0],
            'getCountForPagination' => 3,
            'getQueueableIds'       => [1, 2, 3],
        ]);

        $builder->shouldReceive('skip')->andReturnUsing(function ($value) {
            $this->skip = $value;
        });

        $builder->shouldReceive('take')->andReturnUsing(function ($value) {
            $this->take = $value;
        });

        $builder->shouldReceive('get')->andReturnUsing(function ($value) use ($builder) {
            switch ($value) {
                case ['*']:
                    return (new Collection($this->data))->slice($this->skip, $this->take);
                case ['id']:
                    return $builder;
            }
        });

        $builder->shouldReceive('toBase')->andReturnSelf();

        $this->resultSet = new QueryResources($builder);
    }

    public function tearDown()
    {
        parent::tearDown();
        Mockery::close();
    }

    /** @test */
    public function testGet()
    {
        $this->assertInstanceOf(
            Collection::class,
            $this->resultSet->get()
        );

        $this->assertEquals(
            [['id' => 1], ['id' => 2], ['id' => 3]],
            $this->resultSet->get()->toArray()
        );
    }

    /** @test */
    public function testCount()
    {
        $this->assertEquals(
            3,
            $this->resultSet->count()
        );
    }

    /** @test */
    public function testOffset()
    {
        $this->resultSet->offset(1);
        $this->assertEquals(
            3,
            $this->resultSet->count(),
            'Offset should not influence the count'
        );

        $this->assertEquals(
            [['id' => 2], ['id' => 3]],
            array_values($this->resultSet->get()->toArray())
        );
    }

    /** @test */
    public function testLimit()
    {
        $this->resultSet->limit(1);
        $this->assertEquals(
            3,
            $this->resultSet->count(),
            'Limit should not influence the count'
        );

        $this->assertEquals(
            [['id' => 1]],
            $this->resultSet->get()->toArray()
        );
    }

    /** @test */
    public function testFirst()
    {
        $this->assertEquals(
            $this->data[0],
            $this->resultSet->first()
        );
    }

    /** @test */
    public function testGetIds()
    {
        $this->assertEquals(
            [1, 2, 3],
            $this->resultSet->getIds()
        );
    }
}
