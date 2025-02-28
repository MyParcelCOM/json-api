<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Traits;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use MyParcelCom\JsonApi\Tests\Mocks\AssertionsMock;
use PHPUnit\Framework\TestCase;

class AssertionsTraitTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private AssertionsMock $testClass;

    protected function setUp(): void
    {
        parent::setUp();

        $this->testClass = new AssertionsMock($this);
    }

    public function testAssertJsonSchema(): void
    {
        $this->testClass->assertJsonSchema('swag', 'human', ['head'], [], 'GET', 101);
    }

    public function testAssertJsonDataCount(): void
    {
        $this->testClass->assertJsonDataCount(2, 'human', ['head']);
        $this->testClass->assertJsonDataCount(1, 'human', ['tail']);
        $this->testClass->assertJsonDataCount(0, 'human', ['horn']);
    }

    public function testAssertJsonDataContainsIds(): void
    {
        $this->testClass->assertJsonDataContainsIds('human', ['0', '1'], ['head']);
    }
}
