<?php declare(strict_types=1);

namespace MyParcelCom\Common\Traits;

use JsonSchema\Validator;

/**
 * This trait can be used to extend the Phpunit assertions inside a Laravel project.
 * The Validator class dependencies should me resolved out of the IoC container.
 * The json schema itself should be resolved out of the IoC container with 'schema.'
 */
trait JsonApiAssertionsTrait
{
    /**
     * @param string $schemaPath
     * @param string $url
     * @param array  $headers
     * @param array  $body
     * @param string $method
     * @param int    $status
     */
    public function assertJsonSchema(string $schemaPath, string $url, array $headers = [], array $body = [], string $method = 'get', int $status = 200)
    {
        $response = $this->json($method, $url, $body, $headers);
        $response->assertStatus($status);

        // Content should adhere to the schema.
        $content = json_decode($response->getContent());

        $schema = $this->getSchema($schemaPath, $method, $status);

        /** @var Validator $validator */
        $validator = $this->app->make(Validator::class);
        $validator->validate($content, $schema);

        $this->assertTrue($validator->isValid(), print_r($validator->getErrors(), true));
    }

    /**
     * @param int    $count
     * @param string $url
     * @param array  $headers
     * @param int    $status
     * @internal param null|string $accessToken
     */
    public function assertJsonDataCount(int $count, string $url, array $headers = [], int $status = 200): void
    {
        $response = $this->json('GET', $url, [], $headers);
        $response->assertStatus($status);

        $content = json_decode($response->getContent());

        $this->assertTrue(property_exists($content, 'data'), 'content has no property "data" for url:' . $url);
        $this->assertCount($count, $content->data, 'data amount is ' . count($content->data) . ' expecting ' . $count . ' for url:' . $url);
    }

    /**
     * @param string $schemaPath
     * @param string $method
     * @param int    $status
     * @return \stdClass
     */
    public function getSchema(string $schemaPath, string $method = 'get', int $status = 200): \stdClass
    {
        return $this->app->make('schema')->paths->{$schemaPath}->{strtolower($method)}->responses->{$status}->schema;
    }
}
