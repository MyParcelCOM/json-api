<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Transformers;

use Illuminate\Database\Eloquent\Model;
use Mockery;
use MyParcelCom\JsonApi\Transformers\AbstractTransformer;
use MyParcelCom\JsonApi\Transformers\TransformerFactory;
use MyParcelCom\JsonApi\Transformers\TransformerItem;
use PHPUnit\Framework\TestCase;

class TransformerItemTest extends TestCase
{
    /** @var array */
    protected $transformerData;

    /** @var array */
    protected $includedResource;

    /** @var array */
    protected $includedData;

    /** @var TransformerFactory */
    protected $transformerFactory;

    /** @var TransformerItem */
    protected $transformerItem;

    protected function setUp()
    {
        parent::setUp();

        $this->transformerData = [
            'resourceName' => [
                'data' => [
                    'id'   => 'a',
                    'type' => 'b',
                ],
            ],
        ];

        $this->includedResource = [
            'id'   => 'a',
            'type' => 'b',
        ];
        $this->includedData = [
            'resourceName' => function () {
                return $this->includedResource;
            },
        ];

        $this->transformerFactory = Mockery::mock(TransformerFactory::class, [
            'createFromModel'       => Mockery::mock(AbstractTransformer::class, [
                'getIncluded'      => $this->includedData,
                'getRelationships' => $this->transformerData,
                'transform'        => $this->transformerData,
            ]),
            'createTransformerItem' => Mockery::mock(TransformerItem::class, [
                'getData' => $this->includedResource,
            ]),
        ]);
        $this->transformerItem = new TransformerItem($this->transformerFactory, Mockery::mock(Model::class));
    }

    protected function tearDown()
    {
        parent::tearDown();

        Mockery::close();
    }

    /** @test */
    public function testGetData()
    {
        $this->assertEquals($this->transformerData, $this->transformerItem->getData());
    }

    /** @test */
    public function testGetIncluded()
    {
        $this->assertEquals([$this->includedResource], $this->transformerItem->getIncluded(['resourceName'], []));
        $this->assertEquals([], $this->transformerItem->getIncluded(['resourceName'], [$this->includedResource]));
        $this->assertEquals([], $this->transformerItem->getIncluded([], [$this->includedResource]));
        $this->assertEquals([], $this->transformerItem->getIncluded([], []));
        $this->assertEquals([], $this->transformerItem->getIncluded(['foo'], []));
        $this->assertEquals([], $this->transformerItem->getIncluded(['foo'], ['foo']));
        $this->assertEquals([], $this->transformerItem->getIncluded([], ['foo']));
    }
}
