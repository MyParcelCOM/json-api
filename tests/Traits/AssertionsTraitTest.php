<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Traits;

use Mockery;
use MyParcelCom\JsonApi\Tests\Mocks\AssertionsMock;
use PHPUnit\Framework\TestCase;

class AssertionsTraitTest extends TestCase
{
    /** @var AssertionsMock */
    private $testClass;

    protected function setUp(): void
    {
        parent::setUp();

        $this->testClass = new AssertionsMock($this);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        Mockery::close();
    }

    /** @test */
    public function testAssertJsonSchema()
    {
        $this->testClass->assertJsonSchema('swag', 'human', ['head'], [], 'GET', 101);
    }

    /** @test */
    public function testAssertJsonDataCount()
    {
        $this->testClass->assertJsonDataCount(2, 'human', ['head']);
    }

    /** @test */
    public function testAssertJsonDataContainsIds()
    {
        $this->testClass->assertJsonDataContainsIds('human', ['0', '1'], ['head']);
    }
}
