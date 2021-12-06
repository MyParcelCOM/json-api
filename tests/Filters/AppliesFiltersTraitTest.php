<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Mockery;
use MyParcelCom\JsonApi\Exceptions\UnprocessableEntityException;
use MyParcelCom\JsonApi\Tests\Mocks\Filters\AppliesFiltersMock;
use PHPUnit\Framework\TestCase;

class AppliesFiltersTraitTest extends TestCase
{
    public function tearDown(): void
    {
        parent::tearDown();

        Mockery::close();
    }

    /** @test */
    public function testApplyFiltersToQuery()
    {
        $queryMock = Mockery::mock(QueryBuilder::class);
        $queryMock->shouldReceive('where')->andReturnUsing(function ($closure) {
            $param = Mockery::mock(QueryBuilder::class);
            $param->shouldReceive('orWhere')->andReturnUsing(function ($columnName, $operator, $values) {
                $this->assertEquals('sugar', $columnName);
                $this->assertEquals('nope', $operator);
                $this->assertEquals('black', $values);
            });

            $closure($param);

            return $param;
        });

        $builderMock = Mockery::mock(Builder::class, [
            'getQuery' => $queryMock,
        ]);

        $filters = [
            'coffee' => 'black',
            'tea'    => 'white',
        ];

        // Execute the protected function of the trait. Because of the filters known to the mock, it should:
        // - accept black coffee
        // - ignore white tea
        (new AppliesFiltersMock())->applyFilters($filters, $builderMock);
    }

    /** @test */
    public function testApplyDateFiltersDateFormat()
    {
        $queryMock = Mockery::mock(QueryBuilder::class);
        $queryMock->shouldReceive('where')->andReturnUsing(function ($closure) {
            $param = Mockery::mock(QueryBuilder::class);
            $param->shouldReceive('orWhere')->andReturnUsing(function ($columnName, $operator, $values) {
                $this->assertEquals('created_at', $columnName);
                $this->assertEquals('>=', $operator);
                $this->assertEquals('1987-03-17 00:00:00', $values);
            });

            $closure($param);

            return $param;
        });

        $builderMock = Mockery::mock(Builder::class, [
            'getQuery' => $queryMock,
        ]);

        (new AppliesFiltersMock())->applyFilters(['date_from' => '1987-03-17'], $builderMock);
    }

    /** @test */
    public function testApplyDateFiltersTimestamp()
    {
        $queryMock = Mockery::mock(QueryBuilder::class);
        $queryMock->shouldReceive('where')->once()->andReturnUsing(function ($closure) {
            $param = Mockery::mock(QueryBuilder::class);
            $param->shouldReceive('orWhere')->once()->andReturnUsing(function ($columnName, $operator, $values) {
                $this->assertEquals('created_at', $columnName);
                $this->assertEquals('>=', $operator);
                $this->assertEquals('2021-12-06 16:18:36', $values);
            });

            $closure($param);

            return $param;
        });

        $builderMock = Mockery::mock(Builder::class, [
            'getQuery' => $queryMock,
        ]);

        (new AppliesFiltersMock())->applyFilters(['date_from' => '1638807516'], $builderMock);
    }

    /** @test */
    public function testApplyDateFiltersException()
    {
        $builderMock = Mockery::mock(Builder::class, [
            'getQuery' => Mockery::mock(QueryBuilder::class),
        ]);

        $this->expectException(UnprocessableEntityException::class);

        (new AppliesFiltersMock())->applyFilters(['date_from' => 'invalid'], $builderMock);
    }
}
