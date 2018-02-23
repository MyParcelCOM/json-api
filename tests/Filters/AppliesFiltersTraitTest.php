<?php declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Mockery;
use MyParcelCom\JsonApi\Tests\Mocks\Filters\AppliesFiltersMock;
use PHPUnit\Framework\TestCase;

class AppliesFiltersTraitTest extends TestCase
{
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
}
