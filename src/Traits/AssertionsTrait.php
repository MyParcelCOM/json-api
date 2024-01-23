<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Traits;

use Illuminate\Testing\TestResponse;
use JsonSchema\Validator;
use MyParcelCom\JsonApi\Http\Interfaces\RequestInterface;
use stdClass;

/**
 * This trait can be used to extend the Phpunit assertions inside a Laravel project.
 * The Validator class dependencies should be resolved out of the IoC container.
 * The json schema itself should be resolved out of the IoC container with 'schema'.
 */
trait AssertionsTrait
{
    public function assertJsonSchema(
        string $schemaPath,
        string $url,
        array $headers = [],
        array $body = [],
        string $method = 'get',
        int $status = 200,
    ): TestResponse {
        /** @var TestResponse $response */
        $response = $this->json($method, $url, $body, $headers);
        $accept = $headers['Accept'] ?? RequestInterface::CONTENT_TYPE_JSON_API;

        // Response should have correct header and status.
        $response->assertStatus($status);
        $response->assertHeader('Content-Type', $accept);

        // Content should adhere to the schema.
        $content = json_decode($response->getContent());
        $schema = $this->getSchema($schemaPath, $method, $status, $accept);

        $this->assertValidJsonSchema($content, $schema);

        return $response;
    }

    private function assertValidJsonSchema($content, $schema): void
    {
        $validator = $this->getValidator();
        $validator->validate($content, $schema);

        $this->assertTrue($validator->isValid(), print_r([
            'errors' => $validator->getErrors(),
            'data'   => $content,
        ], true));
    }

    public function assertJsonDataCount(int $count, string $url, array $headers = []): TestResponse
    {
        $response = $this->json('GET', $url, [], $headers);
        $content = json_decode($response->getContent());

        $this->assertTrue(property_exists($content, 'data'), 'content has no property "data" for url: ' . $url);
        if (is_array($content->data)) {
            $this->assertEquals(
                $count,
                count($content->data),
                'data amount is ' . count($content->data) . ' expecting ' . $count . ' for url:' . $url,
            );
        } elseif (is_object($content->data)) {
            // It is a single object, so count is one.
            $this->assertEquals($count, 1);
        } elseif (is_null($content->data)) {
            $this->assertEquals($count, 0);
        } else {
            $this->fail(
                "The content of the data attribute is of type: %s. It should be an array, object or null.",
                gettype($content->data),
            );
        }

        return $response;
    }

    public function assertJsonDataContainsIds(string $url, array $ids = [], array $headers = []): void
    {
        $response = $this->json('GET', $url, [], $headers);
        $content = json_decode($response->getContent());

        $this->assertTrue(property_exists($content, 'data'), 'content has no property "data" for url: ' . $url);
        $data = is_array($content->data) ? $content->data : [$content->data];
        $this->assertCount(
            count($ids),
            $data,
            'Number of expected id\'s did not match number of resources in the response',
        );

        $expectedIds = array_map(fn ($item) => $item->id, $content->data);
        $this->assertEqualsCanonicalizing($ids, $expectedIds, 'Missing expected ids');
    }

    abstract protected function getSchema(
        string $schemaPath,
        string $method = 'get',
        int $status = 200,
        string $accept = RequestInterface::CONTENT_TYPE_JSON_API,
    ): stdClass;

    abstract protected function getValidator(): Validator;
}
