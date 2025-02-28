<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Transformers;

use ArrayIterator;
use Illuminate\Support\Collection;
use Mockery;
use MyParcelCom\JsonApi\Transformers\AbstractTransformer;
use MyParcelCom\JsonApi\Transformers\TransformerCollection;
use MyParcelCom\JsonApi\Transformers\TransformerFactory;
use MyParcelCom\JsonApi\Transformers\TransformerItem;
use PHPUnit\Framework\TestCase;

class TransformerCollectionTest extends TestCase
{
    use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    protected array $transformerData;

    protected array $includedResource;

    protected array $includedData;

    protected TransformerFactory $transformerFactory;

    protected TransformerCollection $transformerCollection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->transformerData = ['resourceName' => ['data' => ['id' => 'a', 'type' => 'b']]];

        $this->includedResource = ['id' => 'a', 'type' => 'b'];
        $this->includedData = [
            'resourceName' => function () {
                return $this->includedResource;
            },
        ];

        $this->transformerFactory = Mockery::mock(TransformerFactory::class, [
            'createFromModel'       => Mockery::mock(
                AbstractTransformer::class,
                [
                    'getIncluded'      => $this->includedData,
                    'getRelationships' => $this->transformerData,
                    'transform'        => $this->transformerData,
                ],
            ),
            'createTransformerItem' => Mockery::mock(TransformerItem::class, ['getData' => $this->includedResource]),
        ]);

        $this->transformerCollection = new TransformerCollection(
            $this->transformerFactory,
            $this->getCollectionMock($this->transformerData),
        );
    }

    public function testGetData()
    {
        $this->assertEquals([$this->transformerData], $this->transformerCollection->getData());
    }

    public function testGetIncluded()
    {
        $this->assertEquals([$this->includedResource], $this->transformerCollection->getIncluded(['resourceName'], []));
        $this->assertEquals([], $this->transformerCollection->getIncluded([], [$this->includedResource]));
        $this->assertEquals([], $this->transformerCollection->getIncluded([], []));
        $this->assertEquals([], $this->transformerCollection->getIncluded(['foo'], []));
        $this->assertEquals([], $this->transformerCollection->getIncluded(['foo'], ['foo']));
        $this->assertEquals([], $this->transformerCollection->getIncluded([], ['foo']));
    }

    protected function getCollectionMock(array $items): Collection
    {
        return Mockery::mock(Collection::class, [
            'offsetExists' => false,
            'offsetGet'    => null,
            'getIterator'  => new ArrayIterator($items),
        ]);
    }
}
