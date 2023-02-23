<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Transformers;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Mockery;
use MyParcelCom\JsonApi\Resources\ResourceIdentifier;
use MyParcelCom\JsonApi\Tests\Stubs\TransformerStub;
use MyParcelCom\JsonApi\Transformers\TransformerException;
use MyParcelCom\JsonApi\Transformers\TransformerFactory;
use PHPUnit\Framework\TestCase;
use stdClass;

class AbstractTransformerTest extends TestCase
{
    use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /** @var Model */
    private $model;

    /** @var TransformerStub */
    private $transformer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->model = Mockery::mock(Model::class);

        $transformerFactory = Mockery::mock(TransformerFactory::class, [
            'createFromModel' => new TransformerStub(Mockery::mock(TransformerFactory::class)),
        ]);
        $this->transformer = new TransformerStub($transformerFactory);
    }

    /** @test */
    public function testTransform()
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

    /** @test */
    public function testGetTypeException()
    {
        $this->expectException(TransformerException::class);
        $this->transformer->clearType()->getType();
    }

    /** @test */
    public function testTransformRelationship()
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
            $this->transformer->transformRelationship($this->model)
        );
    }

    /** @test */
    public function testGetAttributesFromModel()
    {
        $this->assertNull($this->transformer->getAttributesFromModel(null));
        $this->assertEquals(
            [
                'at' => 'tribute',
            ],
            $this->transformer->getAttributesFromModel($this->model)
        );
    }

    /** @test */
    public function testGetTimestamp()
    {
        $datetime = new DateTime();

        $this->assertNull($this->transformer->getTimestamp(null));
        $this->assertEquals($datetime->getTimestamp(), $this->transformer->getTimestamp($datetime));
    }

    /** @test */
    public function testTransformRelationshipForIdentifier()
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
            $this->transformer->transformRelationshipForIdentifier('joe', 'person', stdClass::class)
        );
    }

    /** @test */
    public function testTransformRelationshipForIdentifiers()
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
                ['related' => 'i-link/to.everyone']
            )
        );
    }

    public function testTransformLinkWithParentIdentifier()
    {
        $identifier = new ResourceIdentifier('borderlands', 'fps', 'gearbox');

        $this->assertEquals('gearbox/borderlands', $this->transformer->getLinkWithParentId($identifier));
    }

    /** @test */
    public function testItAlsoPopulatesMetaInTransformingIdentifiers()
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
