<?php declare(strict_types=1);

namespace MyParcelCom\JsonApi\Exceptions\Tests;

use DateTime;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Mockery;
use MyParcelCom\JsonApi\Tests\Stubs\TransformerStub;
use MyParcelCom\JsonApi\Transformers\TransformerException;
use MyParcelCom\JsonApi\Transformers\TransformerFactory;
use PHPUnit\Framework\TestCase;

class AbstractTransformerTest extends TestCase
{
    /** @var Model */
    private $model;

    /** @var TransformerStub */
    private $transformer;

    protected function setUp()
    {
        parent::setUp();

        $this->model = Mockery::mock(Model::class);

        $transformerFactory = Mockery::mock(TransformerFactory::class, [
            'createFromModel' => new TransformerStub(Mockery::mock(TransformerFactory::class)),
        ]);
        $this->transformer = new TransformerStub($transformerFactory);
    }

    protected function tearDown()
    {
        parent::tearDown();

        Mockery::close();
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
    public function testTransformCollection()
    {
        $this->assertEquals(
            [
                [
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
                ],
            ],
            $this->transformer->transformCollection(new Collection([$this->model]))
        );
    }

    /** @test */
    public function testGetAttributesFromCollection()
    {
        $this->assertNull($this->transformer->getAttributesFromCollection(null));
        $this->assertEquals(
            [
                'at' => 'tribute',
            ],
            $this->transformer->getAttributesFromModel(new Collection([$this->model]))
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
    public function testTransformRelationshipForId()
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
            $this->transformer->transformRelationshipForId('1', 'stdClass')
        );
    }

    /** @test */
    public function testTransformRelationshipForIds()
    {
        $this->assertEquals(
            [
                'data' => [
                    [
                        'id'   => 'mockId',
                        'type' => 'test',
                    ],
                    [
                        'id'   => 'mockId',
                        'type' => 'test',
                    ],
                ],
                'links' => [
                    'related' => 'ploink',
                ],
            ],
            $this->transformer->transformRelationshipForIds(['1', '2'], 'stdClass', 'ploink')
        );
    }
}
