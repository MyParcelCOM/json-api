<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Mocks;

use Framework\TestCase;
use Illuminate\Foundation\Testing\TestResponse;
use JsonSchema\Validator;
use Mockery;
use Mockery\Exception;
use MyParcelCom\JsonApi\Traits\AssertionsTrait;
use stdClass;

class AssertionsMock
{
    use AssertionsTrait;

    /** @var TestCase */
    private $testCase;

    /**
     * AssertionsMock constructor.
     *
     * @param $testCase
     */
    public function __construct($testCase)
    {
        $this->testCase = $testCase;
    }

    /**
     * @param string $schemaPath
     * @param string $method
     * @param int    $status
     * @param string $accept
     * @return stdClass
     */
    protected function getSchema(string $schemaPath, string $method = 'get', int $status = 200, string $accept = 'application/vnd.api+json'): stdClass
    {
        return json_decode('{"paths":{"swag":{"get":{"responses":{"101":{"schema":{"data":[{"id":0},{"id":1}]}}}}}}}');
    }

    /**
     * @return Validator
     */
    protected function getValidator(): Validator
    {
        $validatorMock = Mockery::mock(Validator::class, [
            'validate'  => true,
            'isValid'   => true,
            'getErrors' => [],
        ]);

        return $validatorMock;
    }

    public function json($method, $url, $body, $headers)
    {
        $responseMock = Mockery::mock(TestResponse::class);
        $responseMock->shouldReceive('assertStatus')->withArgs([101]);
        $responseMock->shouldReceive('assertHeader')->withArgs(['Content-Type', 'application/vnd.api+json']);
        $responseMock->shouldReceive('getContent')->andReturnUsing(function () use ($method, $url, $body, $headers) {
            if (json_encode([$method, $url, $body, $headers]) !== '["GET","human",[],["head"]]') {
                throw new Exception('unexpected json() parameters');
            }

            return '{"data":[{"id":0},{"id":1}]}';
        });

        return $responseMock;
    }

    private function assertTrue($condition, $message = '')
    {
        $this->testCase->assertTrue($condition, $message);
    }

    private function assertEquals($expected, $actual)
    {
        $this->testCase->assertEquals($expected, $actual);
    }

    private function assertObjectHasAttribute($attributeName, $object, $message = '')
    {
        $this->testCase->assertObjectHasAttribute($attributeName, $object, $message);
    }

    private function assertCount($expectedCount, $haystack, $message = '')
    {
        $this->testCase->assertCount($expectedCount, $haystack, $message);
    }
}
