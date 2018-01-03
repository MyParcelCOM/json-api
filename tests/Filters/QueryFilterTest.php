<?php declare(strict_types=1);

namespace MyParcelCom\Common\Tests\Filters;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Grammars\PostgresGrammar;
use Illuminate\Database\Query\Processors\PostgresProcessor;
use Mockery;
use MyParcelCom\Common\Filters\QueryFilter;
use PHPUnit\Framework\TestCase;

class QueryFilterTest extends TestCase
{
    /** @var QueryFilter */
    private $queryFilter;

    /** @var Builder */
    private $query;

    protected function setUp()
    {
        parent::setUp();

        $connection = Mockery::mock(ConnectionInterface::class, [
            'getQueryGrammar'  => new PostgresGrammar(),
            'getPostProcessor' => new PostgresProcessor(),
        ]);
        $this->query = new Builder($connection);
        $this->queryFilter = new QueryFilter($this->query);
    }

    protected function tearDown()
    {
        parent::tearDown();

        Mockery::close();
    }

    /** @test */
    public function testApplyWhereValueIsNull()
    {
        $this->queryFilter->apply(['column_a', 'column_b'], '!=', null);
        $this->queryFilter->apply('column_c', 'nOt', null); // Also tests capitalized operator.

        $this->assertEquals(
            'select * where ("column_a" is not null or "column_b" is not null) and ("column_c" is not null)',
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
            'select * where (lower(column_a) like ? or lower(column_b) like ?) and (lower(column_c) like ? or lower(column_c) like ?) and (lower(column_d) like ? or lower(column_e) like ? or lower(column_d) like ? or lower(column_e) like ?)',
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
