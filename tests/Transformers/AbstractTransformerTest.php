<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Transformers;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use MyParcelCom\JsonApi\Resources\ResourceIdentifier;
use MyParcelCom\JsonApi\Tests\Stubs\TransformerStub;
use MyParcelCom\JsonApi\Transformers\TransformerException;
use MyParcelCom\JsonApi\Transformers\TransformerFactory;
use PHPUnit\Framework\TestCase;
use stdClass;
use UnitEnum;

enum TestUnitEnum
{
    case test;
}

enum TestBackedEnum: string
{
    case TYPE = 'test';
}

class AbstractTransformerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private Model $model;

    private TransformerStub $transformer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->model = Mockery::mock(Model::class);

        $transformerFactory = Mockery::mock(TransformerFactory::class, [
            'createFromModel' => new TransformerStub(Mockery::mock(TransformerFactory::class)),
        ]);
        $this->transformer = new TransformerStub($transformerFactory);
    }

    public function testTransform(): void
    {
        $this->assertEquals([
            'id'   => 'mockId',
            'type' => 'test',
        ], $this->transformer->transformIdentifier($this->model));

        $this->assertEquals([
            'id'            => 'mockId',
            'type'          => 'test',
            'attributes'    => [
                'at' => 'tribute',
            ],
            'meta'          => [
                'da' => 'ta',
            ],
            'links'         => [
                'self' => '#32',
            ],
            'relationships' => [
                'relation' => 'ship',
            ],
        ], $this->transformer->transform($this->model));

        $this->assertEquals(['more' => 'things'], $this->transformer->getIncluded($this->model));

        $this->assertEmpty($this->transformer->getRelationLink($this->model));
    }

    public function testTypeUsingUnitEnum(): void
    {
        $transformer = new class extends TransformerStub {
            protected string $type = '';
            protected UnitEnum $resourceType = TestUnitEnum::test;

            public function __construct()
            {
                parent::__construct(Mockery::mock(TransformerFactory::class));
            }
        };

        $this->assertSame('test', $transformer->getType());
    }

    public function testTypeUsingBackedEnum(): void
    {
        $transformer = new class extends TransformerStub {
            protected string $type = '';
            protected UnitEnum $resourceType = TestBackedEnum::TYPE;

            public function __construct()
            {
                parent::__construct(Mockery::mock(TransformerFactory::class));
            }
        };

        $this->assertSame('test', $transformer->getType());
    }

    public function testGetTypeException(): void
    {
        $transformer = new class extends TransformerStub {
            protected string $type = '';

            public function __construct()
            {
                parent::__construct(Mockery::mock(TransformerFactory::class));
            }
        };

        $this->expectException(TransformerException::class);
        $transformer->getType();
    }

    public function testTransformRelationship(): void
    {
        $this->assertEquals(
            [
                'data'  => [
                    'id'   => 'mockId',
                    'type' => 'test',
                ],
                'links' => [
                    'related' => '#32',
                ],
            ],
            $this->transformer->transformRelationship($this->model),
        );
    }

    public function testGetAttributesFromModel(): void
    {
        $this->assertNull($this->transformer->getAttributesFromModel(null));
        $this->assertEquals(
            [
                'at' => 'tribute',
            ],
            $this->transformer->getAttributesFromModel($this->model),
        );
    }

    public function testGetTimestamp(): void
    {
        $datetime = new DateTime();

        $this->assertNull($this->transformer->getTimestamp(null));
        $this->assertEquals($datetime->getTimestamp(), $this->transformer->getTimestamp($datetime));
    }

    public function testTransformRelationshipForIdentifier(): void
    {
        $this->assertEquals(
            [
                'data'  => [
                    'id'   => 'joe',
                    'type' => 'person',
                ],
                'links' => [
                    'related' => '#32',
                ],
            ],
            $this->transformer->transformRelationshipForIdentifier('joe', 'person', stdClass::class),
        );
    }

    public function testTransformRelationshipForIdentifiers(): void
    {
        $this->assertEquals(
            [
                'data'  => [
                    [
                        'id'   => 'joe',
                        'type' => 'person',
                    ],
                    [
                        'id'   => 'jane',
                        'type' => 'person',
                    ],
                    [
                        'id'   => 'pete',
                        'type' => 'person',
                    ],
                    [
                        'id'   => 'anna',
                        'type' => 'person',
                    ],
                ],
                'links' => [
                    'related' => 'i-link/to.everyone',
                ],
            ],
            $this->transformer->transformRelationshipForIdentifiers(
                [
                    'joe',
                    'jane',
                    'pete',
                    'anna',
                ],
                'person',
                ['related' => 'i-link/to.everyone'],
            ),
        );
    }

    public function testTransformLinkWithParentIdentifier(): void
    {
        $identifier = new ResourceIdentifier('borderlands', 'fps', 'gearbox');

        $this->assertEquals('gearbox/borderlands', $this->transformer->getLinkWithParentId($identifier));
    }

    public function testItAlsoPopulatesMetaInTransformingIdentifiers(): void
    {
        $this->assertEquals([
            'id'   => 'mockId',
            'type' => 'test',
            'meta' => [
                'da' => 'ta',
            ],
        ], $this->transformer->transformIdentifier($this->model, true));
    }
}
