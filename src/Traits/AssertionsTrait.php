<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Traits;

use Illuminate\Foundation\Testing\TestResponse;
use JsonSchema\Validator;

/**
 * This trait can be used to extend the Phpunit assertions inside a Laravel project.
 * The Validator class dependencies should me resolved out of the IoC container.
 * The json schema itself should be resolved out of the IoC container with 'schema'.
 */
trait AssertionsTrait
{
    /**
     * @param string $schemaPath
     * @param string $url
     * @param array  $headers
     * @param array  $body
     * @param string $method
     * @param int    $status
     * @return TestResponse
     */
    public function assertJsonSchema(string $schemaPath, string $url, array $headers = [], array $body = [], string $method = 'get', int $status = 200): TestResponse
    {
        /** @var TestResponse $response */
        $response = $this->json($method, $url, $body, $headers);

        // Response should have correct header and status.
        $response->assertStatus($status);
        $response->assertHeader('Content-Type', 'application/vnd.api+json');

        $accept = $headers['Accept'] ?? 'application/vnd.api+json';

        // Content should adhere to the schema.
        $content = json_decode($response->getContent());
        $schema = $this->getSchema($schemaPath, $method, $status, $accept);

        $this->assertValidJsonSchema($content, $schema);

        return $response;
    }

    /**
     * @param $content
     * @param $schema
     */
    private function assertValidJsonSchema($content, $schema): void
    {
        /** @var Validator $validator */
        $validator = $this->getValidator();
        $validator->validate($content, $schema);

        $this->assertTrue($validator->isValid(), print_r([
            'errors' => $validator->getErrors(),
            'data'   => $content,
        ], true));
    }

    /**
     * @param int    $count
     * @param string $url
     * @param array  $headers
     * @return TestResponse
     */
    public function assertJsonDataCount(int $count, string $url, array $headers = []): TestResponse
    {
        $response = $this->json('GET', $url, [], $headers);
        $content = json_decode($response->getContent());

        $this->assertTrue(property_exists($content, 'data'), 'content has no property "data" for url:' . $url);
        if (is_array($content->data)) {
            $this->assertEquals($count, count($content->data), 'data amount is ' . count($content->data) . ' expecting ' . $count . ' for url:' . $url);
        } elseif (is_object($content->data)) {
            // It is a single object, so count is one.
            $this->assertEquals($count, 1);
        } elseif (is_null($content->data)) {
            $this->assertEquals($count, 0);
        } else {
            $this->fail("The content of the data attribute is of type: %s. It should be an array, object or null.", gettype($content->data));
        }

        return $response;
    }

    /**
     * @param string $url
     * @param array  $headers
     * @param array  $ids
     */
    private function assertJsonDataContainsIds(string $url, array $ids = [], array $headers = [])
    {
        $response = $this->json('GET', $url, [], $headers);
        $content = json_decode($response->getContent());

        $this->assertTrue(property_exists($content, 'data'), print_r($content, true));
        $data = is_array($content->data) ? $content->data : [$content->data];
        $this->assertCount(count($ids), $data, 'Number of expected id\'s did not match number of resources in the response');

        $expectedIds = array_map(function ($item) {
            return $item->id;
        }, $content->data);
        $this->assertEquals($ids, $expectedIds, 'Missing expected ids', 0.0, 10, true);
    }

    /**
     * @param string $schemaPath
     * @param string $method
     * @param int    $status
     * @param string $accept
     * @return stdClass
     */
    protected abstract function getSchema(string $schemaPath, string $method = 'get', int $status = 200, string $accept = 'application/vnd.api+json'): stdClass;

    /**
     * @return Validator
     */
    protected abstract function getValidator(): Validator;
}
