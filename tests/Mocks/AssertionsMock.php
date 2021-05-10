<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Mocks;

use Illuminate\Testing\TestResponse;
use JsonSchema\Validator;
use Mockery;
use Mockery\Exception;
use MyParcelCom\JsonApi\Traits\AssertionsTrait;
use PHPUnit\Framework\TestCase;
use stdClass;

class AssertionsMock
{
    use AssertionsTrait;

    /** @var TestCase */
    private $testCase;

    /**
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
        return Mockery::mock(Validator::class, [
            'validate'  => true,
            'isValid'   => true,
            'getErrors' => [],
        ]);
    }

    public function json($method, $url, $body, $headers)
    {
        $responseMock = Mockery::mock(TestResponse::class);
        $responseMock->shouldReceive('assertStatus')->withArgs([101]);
        $responseMock->shouldReceive('assertHeader')->withArgs(['Content-Type', 'application/vnd.api+json']);
        $responseMock->shouldReceive('getContent')->andReturnUsing(function () use ($method, $url, $body, $headers) {
            switch (json_encode([$method, $url, $body, $headers])) {
                case '["GET","human",[],["head"]]':
                    return '{"data":[{"id":0},{"id":1}]}';
                case '["GET","human",[],["tail"]]':
                    return '{"data":{"id":2}}';
                case '["GET","human",[],["horn"]]':
                    return '{"data":null}';
                default:
                    throw new Exception('unexpected json() parameters');
            }
        });

        return $responseMock;
    }

    private function assertTrue($condition, $message = '')
    {
        $this->testCase->assertTrue($condition, $message);
    }

    private function assertEquals($expected, $actual, $message = '')
    {
        $this->testCase->assertEquals($expected, $actual, $message);
    }

    private function assertEqualsCanonicalizing($expected, $actual, $message = '')
    {
        $this->testCase->assertEqualsCanonicalizing($expected, $actual, $message);
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
