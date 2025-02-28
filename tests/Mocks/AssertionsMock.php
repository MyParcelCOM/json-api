<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Mocks;

use Illuminate\Testing\TestResponse;
use JsonSchema\Validator;
use Mockery;
use Mockery\Exception;
use MyParcelCom\JsonApi\Http\Interfaces\RequestInterface;
use MyParcelCom\JsonApi\Traits\AssertionsTrait;
use PHPUnit\Framework\TestCase;
use stdClass;

class AssertionsMock
{
    use AssertionsTrait;

    public function __construct(
        private TestCase $testCase,
    ) {
    }

    protected function getSchema(
        string $schemaPath,
        string $method = 'get',
        int $status = 200,
        string $accept = RequestInterface::CONTENT_TYPE_JSON_API,
    ): stdClass {
        return json_decode('{"paths":{"swag":{"get":{"responses":{"101":{"schema":{"data":[{"id":0},{"id":1}]}}}}}}}');
    }

    protected function getValidator(): Validator
    {
        return Mockery::mock(Validator::class, [
            'validate'  => true,
            'isValid'   => true,
            'getErrors' => [],
        ]);
    }

    public function json($method, $url, $body, $headers): TestResponse
    {
        $responseMock = Mockery::mock(TestResponse::class);
        $responseMock->shouldReceive('assertStatus')->withArgs([101]);
        $responseMock->shouldReceive('assertHeader')->withArgs(['Content-Type', 'application/vnd.api+json']);
        $responseMock->shouldReceive('getContent')->andReturnUsing(function () use ($method, $url, $body, $headers) {
            return match (json_encode([$method, $url, $body, $headers])) {
                '["GET","human",[],["head"]]' => '{"data":[{"id":0},{"id":1}]}',
                '["GET","human",[],["tail"]]' => '{"data":{"id":2}}',
                '["GET","human",[],["horn"]]' => '{"data":null}',
                default                       => throw new Exception('unexpected json() parameters'),
            };
        });

        return $responseMock;
    }

    private function assertTrue($condition, $message = ''): void
    {
        $this->testCase->assertTrue($condition, $message);
    }

    private function assertEquals($expected, $actual, $message = ''): void
    {
        $this->testCase->assertEquals($expected, $actual, $message);
    }

    private function assertEqualsCanonicalizing($expected, $actual, $message = ''): void
    {
        $this->testCase->assertEqualsCanonicalizing($expected, $actual, $message);
    }

    private function assertCount($expectedCount, $haystack, $message = ''): void
    {
        $this->testCase->assertCount($expectedCount, $haystack, $message);
    }
}
