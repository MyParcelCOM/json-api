<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Transformers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Mockery;
use MyParcelCom\JsonApi\Transformers\AbstractTransformer;
use MyParcelCom\JsonApi\Transformers\TransformerCollection;
use MyParcelCom\JsonApi\Transformers\TransformerFactory;
use MyParcelCom\JsonApi\Transformers\TransformerItem;
use PHPUnit\Framework\TestCase;

class TransformerItemTest extends TestCase
{
    use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    protected array $transformerData;

    protected array $includedRelationship;

    protected array $includedResource;

    protected array $includedData;

    protected AbstractTransformer $transformer;

    protected TransformerFactory $transformerFactory;

    protected TransformerItem $transformerItem;

    protected function setUp(): void
    {
        parent::setUp();

        $this->includedResource = [
            'id'   => 'a',
            'type' => 'b',
        ];
        $this->includedRelationship = [
            'id'   => 'c',
            'type' => 'd',
        ];
        $this->includedData = [
            'resourceName'       => function () {
                return $this->includedResource;
            },
            'resourceCollection' => function () {
                return new Collection([
                    $this->includedResource,
                    $this->includedResource,
                ]);
            },
        ];
        $this->transformerData = [
            'resourceName'       => [
                'data' => $this->includedResource,
            ],
            'resourceCollection' => [
                'data' => $this->includedResource,
            ],
        ];

        $this->transformerFactory = Mockery::mock(TransformerFactory::class, [
            'createFromModel'             => Mockery::mock(AbstractTransformer::class, [
                'getIncluded'      => $this->includedData,
                'getRelationships' => $this->transformerData,
                'transform'        => $this->transformerData,
            ]),
            'createTransformerItem'       => Mockery::mock(TransformerItem::class, [
                'getData'     => $this->includedResource,
                'getIncluded' => [$this->includedRelationship],
            ]),
            'createTransformerCollection' => Mockery::mock(TransformerCollection::class, [
                'getData'     => [$this->includedResource, $this->includedResource],
                'getIncluded' => [$this->includedRelationship, $this->includedRelationship],
            ]),
        ]);
        $this->transformerItem = new TransformerItem($this->transformerFactory, Mockery::mock(Model::class));
    }

    public function testGetData()
    {
        $this->assertEquals($this->transformerData, $this->transformerItem->getData());
    }

    public function testGetIncluded()
    {
        $this->assertEquals([$this->includedResource], $this->transformerItem->getIncluded(['resourceName'], []));
        $this->assertEquals(
            [$this->includedResource],
            $this->transformerItem->getIncluded(['resourceName'], [], ['relationships' => $this->transformerData]),
        );
        $this->assertEquals([], $this->transformerItem->getIncluded([], [$this->includedResource]));
        $this->assertEquals([], $this->transformerItem->getIncluded([], []));
        $this->assertEquals([], $this->transformerItem->getIncluded(['foo'], []));
        $this->assertEquals([], $this->transformerItem->getIncluded(['foo'], ['foo']));
        $this->assertEquals([], $this->transformerItem->getIncluded([], ['foo']));
        $this->assertEquals(
            [$this->includedRelationship],
            $this->transformerItem->getIncluded(['resourceName' => ['relatedResource']]),
        );
    }

    public function testGetIncludedCollection()
    {
        $transformerItem = new TransformerItem($this->transformerFactory, Mockery::mock(Collection::class));

        $this->assertEquals(
            [$this->includedResource, $this->includedResource],
            $transformerItem->getIncluded(['resourceCollection'], []),
        );
        $this->assertEquals(
            [$this->includedRelationship, $this->includedRelationship],
            $this->transformerItem->getIncluded(['resourceCollection' => ['relatedCollection']]),
        );
    }
}
