<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Filters;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Grammars\PostgresGrammar;
use Illuminate\Database\Query\Processors\PostgresProcessor;
use Mockery;
use MyParcelCom\JsonApi\Filters\QueryFilter;
use PHPUnit\Framework\TestCase;

class QueryFilterTest extends TestCase
{
    use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    private QueryFilter $queryFilter;

    private Builder $query;

    protected function setUp(): void
    {
        parent::setUp();

        $connection = Mockery::mock(ConnectionInterface::class, [
            'getQueryGrammar'  => new PostgresGrammar(),
            'getPostProcessor' => new PostgresProcessor(),
        ]);
        $this->query = new Builder($connection);
        $this->queryFilter = new QueryFilter($this->query);
    }

    /** @test */
    public function testApplyWhereValueIsNull()
    {
        $this->queryFilter->apply(['column_a', 'column_b'], '!=', [null]);
        $this->queryFilter->apply('column_c', 'nOt', null); // Also tests capitalized operator.
        $this->queryFilter->apply('column_d', '=', null);

        $this->assertEquals(
            'select * where ("column_a" is not null or "column_b" is not null) and ("column_c" is not null) and ("column_d" is null)',
            $this->query->toSql()
        );
    }

    /** @test */
    public function testApplyWhereOperatorIsLike()
    {
        $this->queryFilter->apply(['column_a', 'column_b'], 'LiKe', 'value_a');
        $this->queryFilter->apply('column_c', 'like', ['value_a', 'value_b']);
        $this->queryFilter->apply(['column_d', 'column_e'], 'LIKE', ['value_c', 'value_d']);

        $this->assertEquals(
            'select * where (lower(column_a)::text like ? or lower(column_b)::text like ?) and (lower(column_c)::text like ? or lower(column_c)::text like ?) and (lower(column_d)::text like ? or lower(column_e)::text like ? or lower(column_d)::text like ? or lower(column_e)::text like ?)',
            $this->query->toSql()
        );

        $this->assertEquals([
            '%value_a%',
            '%value_a%',
            '%value_a%',
            '%value_b%',
            '%value_c%',
            '%value_c%',
            '%value_d%',
            '%value_d%',
        ], $this->query->getBindings());
    }

    /** @test */
    public function testApplyWhereValuesIsArray()
    {
        $this->queryFilter->apply('column_a', '=', ['value_a', 'value_b']);
        $this->queryFilter->apply(['column_b', 'column_c'], '!=', ['value_c', 'value_d']);
        $this->queryFilter->apply('column_d', 'noT', ['value_e', 'value_f']);

        $this->assertEquals(
            'select * where ("column_a" in (?, ?)) and ("column_b" not in (?, ?) or "column_c" not in (?, ?)) and ("column_d" not in (?, ?))',
            $this->query->toSql()
        );

        $this->assertEquals([
            'value_a',
            'value_b',
            'value_c',
            'value_d',
            'value_c',
            'value_d',
            'value_e',
            'value_f',
        ], $this->query->getBindings());
    }

    /** @test */
    public function testFilterQuery()
    {
        $this->queryFilter->apply('column_a', '>', '25');
        $this->queryFilter->apply(['column_b', 'column_c'], '=', 'value_a');

        $this->assertEquals(
            'select * where ("column_a" > ?) and ("column_b" = ? or "column_c" = ?)',
            $this->query->toSql()
        );

        $this->assertEquals([
            '25',
            'value_a',
            'value_a',
        ], $this->query->getBindings());
    }
}
