<?php declare(strict_types=1);

namespace MyParcelCom\JsonApi\Exceptions\Tests;

use Mockery;
use MyParcelCom\JsonApi\Tests\Stubs\TransformerStub;
use MyParcelCom\JsonApi\Transformers\AbstractTransformer;
use MyParcelCom\JsonApi\Transformers\TransformerFactory;
use PHPUnit\Framework\TestCase;
use stdClass;

class AbstractTransformerTest extends TestCase
{
    /** @var AbstractTransformer */
    private $transformer;

    protected function setUp()
    {
        parent::setUp();

        $transformerFactory = Mockery::mock(TransformerFactory::class);
        $this->transformer = new TransformerStub($transformerFactory);
    }

    /** @test */
    public function testTransform()
    {
        $model = Mockery::mock(stdClass::class);

        $this->assertEquals([
            'id'   => 'mockId',
            'type' => 'test',
        ], $this->transformer->transformIdentifier($model));

        $this->assertEquals([
            'id'            => 'mockId',
            'type'          => 'test',
            'relationships' => [
                'relation' => 'ship',
            ],
            'attributes'    => [
                'at' => 'tribute',
            ],
            'meta'          => [
                'da' => 'ta',
            ],
            'links'         => [
                'self' => '#32',
            ],
        ], $this->transformer->transform($model));

        $this->assertEquals(['more' => 'things'], $this->transformer->getIncluded($model));

        $this->assertEmpty($this->transformer->getRelationLink($model));
    }
}
