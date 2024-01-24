<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use MyParcelCom\JsonApi\Exceptions\UnprocessableEntityException;
use MyParcelCom\JsonApi\Tests\Mocks\Filters\AppliesFiltersMock;
use PHPUnit\Framework\TestCase;

class AppliesFiltersTraitTest extends TestCase
{
    use MockeryPHPUnitIntegration;

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
    public function testApplyDateFiltersDateString()
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

    /**
     * @test
     * @dataProvider dateFilterProvider
     */
    public function testApplyDateFiltersISO8601($expectation, $dateFrom)
    {
        $queryMock = Mockery::mock(QueryBuilder::class);
        $queryMock->shouldReceive('where')->andReturnUsing(function ($closure) use ($expectation) {
            $param = Mockery::mock(QueryBuilder::class);
            $param->shouldReceive('orWhere')->andReturnUsing(
                function ($columnName, $operator, $values) use ($expectation) {
                    $this->assertEquals('created_at', $columnName);
                    $this->assertEquals('>=', $operator);
                    $this->assertEquals($expectation, $values);
                },
            );

            $closure($param);

            return $param;
        });

        $builderMock = Mockery::mock(Builder::class, [
            'getQuery' => $queryMock,
        ]);

        (new AppliesFiltersMock())->applyFilters(['date_from' => $dateFrom], $builderMock);
    }

    public static function dateFilterProvider(): array
    {
        return [
            ['2021-12-06 16:18:36', '1638807516'],
            ['2021-12-06 00:00:00', '2021-12-06'],
            ['2021-12-05 23:23:45', '2021-12-06T01:23:45+0200'],
            ['2021-12-06 03:23:45', '2021-12-06T01:23:45-0200'],
            ['2021-12-06 00:23:45', '2021-12-06T01:23:45.123+01:00'],
            ['2021-12-06 02:23:45', '2021-12-06T01:23:45.123456-01:00'],
        ];
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
