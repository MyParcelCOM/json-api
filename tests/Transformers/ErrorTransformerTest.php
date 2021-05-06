<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Transformers;

use Mockery;
use MyParcelCom\JsonApi\Exceptions\Interfaces\JsonSchemaErrorInterface;
use MyParcelCom\JsonApi\Transformers\ErrorTransformer;
use PHPUnit\Framework\TestCase;

class ErrorTransformerTest extends TestCase
{
    /** @test */
    public function testTransform()
    {
        $exception = Mockery::mock(JsonSchemaErrorInterface::class, [
            'getId'        => '123',
            'getLinks'     => [
                'api_specification' => 'The combined address fields exceed the limit of 35 characters.',
            ],
            'getStatus'    => 422,
            'getErrorCode' => '456',
            'getTitle'     => 'Value is too long',
            'getDetail'    => 'The combined address fields exceed the limit of 35 characters.',
            'getSource'    => [
                'pointer' => '/data/attributes/recipient_address/street_1',
            ],
            'getMeta'      => [
                'carrier_rules' => [
                    [
                        "type"  => "max-length",
                        "value" => 35,
                    ],
                    [
                        "type" => "required",
                    ],
                ],
            ],
        ]);

        $transformedError = (new ErrorTransformer())->transform($exception);

        $this->assertSame([
            'id'     => '123',
            'links'  => [
                'api_specification' => 'The combined address fields exceed the limit of 35 characters.',
            ],
            'status' => '422',
            'code'   => '456',
            'title'  => 'Value is too long',
            'detail' => 'The combined address fields exceed the limit of 35 characters.',
            'source' => [
                'pointer' => '/data/attributes/recipient_address/street_1',
            ],
            'meta'   => [
                'carrier_rules' => [
                    [
                        "type"  => "max-length",
                        "value" => 35,
                    ],
                    [
                        "type" => "required",
                    ],
                ],
            ],
        ], $transformedError);
    }
}
