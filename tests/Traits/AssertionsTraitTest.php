<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Traits;

use JsonSchema\Validator;
use Mockery;
use MyParcelCom\JsonApi\Tests\Mocks\AssertionsMock;
use PHPUnit\Framework\TestCase;

class AssertionsTraitTest extends TestCase
{
    /** @var AssertionsMock */
    private $model;

    protected function setUp()
    {
        parent::setUp();

        $this->model = new AssertionsMock($this);
    }

    protected function tearDown()
    {
        parent::tearDown();

        Mockery::close();
    }

    /**
     * Helper function used as a substitute for app->make() to mock the dependencies.
     *
     * @param string $make
     * @return mixed
     */
    public function make($make)
    {
        if ($make === 'schema') {
            return json_decode('{"paths":{"swag":{"get":{"responses":{"101":{"schema":{"data":[404]}}}}}}}');
        }
        if ($make === Validator::class) {
            $validatorMock = Mockery::mock(Validator::class, [
                'isValid'   => true,
                'getErrors' => [],
            ]);
            $validatorMock->shouldReceive('validate')->andReturnUsing(function ($content, $schema) {
                $this->assertEquals(json_encode($schema), json_encode($content));

                return [];
            });

            return $validatorMock;
        }
    }

    /** @test */
    public function testAssertJsonSchema()
    {
        $this->model->assertJsonSchema('swag', 'human', ['head'], [], 'GET', 101);
    }

    /** @test */
    public function testAssertJsonDataCount()
    {
        $this->model->assertJsonDataCount(1, 'human', ['head'], 101);
    }

    /** @test */
    public function testGetSchema()
    {
        $this->assertEquals(
            '{"data":[404]}',
            json_encode($this->model->getSchema('swag', 'GET', 101))
        );
    }
}
