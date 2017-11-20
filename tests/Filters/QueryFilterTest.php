<?php declare(strict_types=1);

namespace MyParcelCom\Common\Tests\Filters;

use Illuminate\Database\Query\Builder;
use Mockery;
use MyParcelCom\Common\Filters\QueryFilter;
use PHPUnit\Framework\TestCase;

class QueryFilterTest extends TestCase
{
    protected function tearDown()
    {
        parent::tearDown();

        Mockery::close();
    }

    /** @test */
    public function testEquals()
    {
        $query = Mockery::mock(Builder::class);
        $query->shouldReceive('where')->andReturnUsing(function ($column, $operator = null, $value = null, $boolean = 'and') use ($query) {
            $this->assertEquals('column_a', $column);
            $this->assertEquals('=', $operator);
            $this->assertEquals('some_value', $value);
            $this->assertEquals('and', strtolower($boolean));

            return $query;
        });

        $filter = new QueryFilter($query);

        $filter->apply('column_a', '=', 'some_value');
    }

    /** @test */
    public function testEqualsArray()
    {
        $query = Mockery::mock(Builder::class);
        $query->shouldReceive('whereIn')->andReturnUsing(function ($column, $values, $boolean = 'and', $not = false) use ($query) {
            $this->assertEquals('column_a', $column);
            $this->assertEquals(['some_value', 'another_value', 'value_c'], $values);
            $this->assertEquals('and', strtolower($boolean));
            $this->assertEquals(false, $not);

            return $query;
        });

        $filter = new QueryFilter($query);

        $filter->apply('column_a', '=', ['some_value', 'another_value', 'value_c']);
    }

    /** @test */
    public function testEqualsNull()
    {
        $query = Mockery::mock(Builder::class);
        $query->shouldReceive('whereNull')->andReturnUsing(function ($column, $boolean = 'and', $not = false) use ($query) {
            $this->assertEquals('column_a', $column);
            $this->assertEquals('and', strtolower($boolean));
            $this->assertEquals(false, $not);

            return $query;
        });

        $filter = new QueryFilter($query);

        $filter->apply('column_a', '=', null);
    }

    /** @test */
    public function testLike()
    {
        $query = Mockery::mock(Builder::class);
        $query->shouldReceive('where')->andReturnUsing(function ($column, $operator = null, $value = null, $boolean = 'and') use ($query) {
            if (is_callable($column)) {
                $column($query);
            } else {
                $this->assertEquals('column_a', $column);
                $this->assertEquals('like', $operator);
                $this->assertEquals('%some_value%', $value);
                $this->assertEquals('or', strtolower($boolean));
            }

            return $query;
        });

        $filter = new QueryFilter($query);

        $filter->apply('column_a', 'like', 'some_value');
    }

    /** @test */
    public function testLikeArray()
    {
        $query = Mockery::mock(Builder::class);
        $expectedValues = ['%some_value%', '%another_value%', '%value_c%'];
        $query->shouldReceive('where')->andReturnUsing(function ($column, $operator = null, $value = null, $boolean = 'and') use ($query, &$expectedValues) {
            if (is_callable($column)) {
                $column($query);
            } else {
                $this->assertEquals('column_a', $column);
                $this->assertEquals('like', $operator);
                $this->assertArraySubset([$value], $expectedValues);
                $this->assertEquals('or', strtolower($boolean));

                $expectedValues = array_values(array_diff($expectedValues, [$value]));
            }

            return $query;
        });

        $filter = new QueryFilter($query);

        $filter->apply('column_a', 'like', ['some_value', 'another_value', 'value_c']);
        $this->assertEmpty(
            $expectedValues,
            'Not all expected values have been added to the query'
        );
    }

    /** @test */
    public function testLikeNull()
    {
        $query = Mockery::mock(Builder::class);
        $query->shouldReceive('whereNull')->andReturnUsing(function ($column, $boolean = 'and', $not = false) use ($query) {
            $this->assertEquals('column_a', $column);
            $this->assertEquals('and', strtolower($boolean));
            $this->assertEquals(false, $not);

            return $query;
        });

        $filter = new QueryFilter($query);

        $filter->apply('column_a', 'like', null);
    }

    /** @test */
    public function testNotEquals()
    {
        $query = Mockery::mock(Builder::class);
        $query->shouldReceive('where')->andReturnUsing(function ($column, $operator = null, $value = null, $boolean = 'and') use ($query) {
            $this->assertEquals('column_a', $column);
            $this->assertEquals('!=', $operator);
            $this->assertEquals('some_value', $value);
            $this->assertEquals('and', strtolower($boolean));

            return $query;
        });

        $filter = new QueryFilter($query);

        $filter->apply('column_a', '!=', 'some_value');
    }

    /** @test */
    public function testNotEqualsArray()
    {
        $query = Mockery::mock(Builder::class);
        $query->shouldReceive('whereIn')->andReturnUsing(function ($column, $values, $boolean = 'and', $not = false) use ($query) {
            $this->assertEquals('column_a', $column);
            $this->assertEquals(['some_value', 'another_value', 'value_c'], $values);
            $this->assertEquals('and', strtolower($boolean));
            $this->assertEquals(true, $not);

            return $query;
        })->shouldReceive('whereNotIn')->andReturnUsing(function ($column, $values, $boolean = 'and') use ($query) {
            $this->assertEquals('column_a', $column);
            $this->assertEquals(['some_value', 'another_value', 'value_c'], $values);
            $this->assertEquals('and', strtolower($boolean));

            return $query;
        });

        $filter = new QueryFilter($query);

        $filter->apply('column_a', '!=', ['some_value', 'another_value', 'value_c']);
    }

    /** @test */
    public function testNotEqualsNull()
    {
        $query = Mockery::mock(Builder::class);
        $query->shouldReceive('whereNull')->andReturnUsing(function ($column, $boolean = 'and', $not = false) use ($query) {
            $this->assertEquals('column_a', $column);
            $this->assertEquals('and', strtolower($boolean));
            $this->assertEquals(true, $not);

            return $query;
        })->shouldReceive('whereNotNull')->andReturnUsing(function ($column, $boolean = 'and') use ($query) {
            $this->assertEquals('column_a', $column);
            $this->assertEquals('and', strtolower($boolean));

            return $query;
        });

        $filter = new QueryFilter($query);

        $filter->apply('column_a', '!=', null);
    }

    /** @test */
    public function testNotLike()
    {
        $query = Mockery::mock(Builder::class);
        $query->shouldReceive('where')->andReturnUsing(function ($column, $operator = null, $value = null, $boolean = 'and') use ($query) {
            if (is_callable($column)) {
                $column($query);
            } else {
                $this->assertEquals('column_a', $column);
                $this->assertEquals('not like', $operator);
                $this->assertEquals('%some_value%', $value);
                $this->assertEquals('or', strtolower($boolean));
            }

            return $query;
        });

        $filter = new QueryFilter($query);

        $filter->apply('column_a', 'not like', 'some_value');
    }

    /** @test */
    public function testNotLikeArray()
    {
        $query = Mockery::mock(Builder::class);
        $expectedValues = ['%some_value%', '%another_value%', '%value_c%'];
        $query->shouldReceive('where')->andReturnUsing(function ($column, $operator = null, $value = null, $boolean = 'and') use ($query, &$expectedValues) {
            if (is_callable($column)) {
                $column($query);
            } else {
                $this->assertEquals('column_a', $column);
                $this->assertEquals('not like', $operator);
                $this->assertArraySubset([$value], $expectedValues);
                $this->assertEquals('or', strtolower($boolean));

                $expectedValues = array_values(array_diff($expectedValues, [$value]));
            }

            return $query;
        });

        $filter = new QueryFilter($query);

        $filter->apply('column_a', 'not like', ['some_value', 'another_value', 'value_c']);
        $this->assertEmpty(
            $expectedValues,
            'Not all expected values have been added to the query'
        );
    }

    /** @test */
    public function testNotLikeNull()
    {
        $query = Mockery::mock(Builder::class);
        $query->shouldReceive('whereNull')->andReturnUsing(function ($column, $boolean = 'and', $not = false) use ($query) {
            $this->assertEquals('column_a', $column);
            $this->assertEquals('and', strtolower($boolean));
            $this->assertEquals(false, $not);

            return $query;
        })->shouldReceive('whereNotNull')->andReturnUsing(function ($column, $boolean = 'and', $not = false) use ($query) {
            $this->assertEquals('column_a', $column);
            $this->assertEquals('and', strtolower($boolean));

            return $query;
        });

        $filter = new QueryFilter($query);

        $filter->apply('column_a', 'not like', null);
    }
}
