<?php declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Transformers;

use Illuminate\Support\Collection;
use Mockery;
use MyParcelCom\JsonApi\Transformers\AbstractTransformer;
use MyParcelCom\JsonApi\Transformers\TransformerCollection;
use MyParcelCom\JsonApi\Transformers\TransformerFactory;
use MyParcelCom\JsonApi\Transformers\TransformerItem;
use PHPUnit\Framework\TestCase;

class TransformerCollectionTest extends TestCase
{
    /** @var array */
    protected $transformerData;

    /** @var array */
    protected $includedResource;

    /** @var array */
    protected $includedData;

    /** @var TransformerFactory */
    protected $transformerFactory;

    /** @var TransformerCollection */
    protected $transformerCollection;

    protected function setUp()
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
                ]
            ),
            'createTransformerItem' => Mockery::mock(TransformerItem::class, ['getData' => $this->includedResource]),
        ]);

        $this->transformerCollection = new TransformerCollection($this->transformerFactory, $this->getCollectionMock($this->transformerData));
    }

    protected function tearDown()
    {
        parent::tearDown();

        Mockery::close();
    }

    /** @test */
    public function testGetData()
    {
        $this->assertEquals([$this->transformerData], $this->transformerCollection->getData());
    }

    /** @test */
    public function testGetIncluded()
    {
        $this->assertEquals([$this->includedResource], $this->transformerCollection->getIncluded(['resourceName'], []));
        $this->assertEquals([], $this->transformerCollection->getIncluded(['resourceName'], [$this->includedResource]));
        $this->assertEquals([], $this->transformerCollection->getIncluded([], [$this->includedResource]));
        $this->assertEquals([], $this->transformerCollection->getIncluded([], []));
        $this->assertEquals([], $this->transformerCollection->getIncluded(['foo'], []));
        $this->assertEquals([], $this->transformerCollection->getIncluded(['foo'], ['foo']));
        $this->assertEquals([], $this->transformerCollection->getIncluded([], ['foo']));
    }

    /**
     * get an iterable Collection mock
     *
     * @param  array $items iterator items
     * @return Collection
     */
    protected function getCollectionMock(array $items)
    {
        return Mockery::mock(Collection::class, [
            'offsetExists' => false,
            'offsetGet'    => null,
            'getIterator'  => $this->getIteratorMock($items),
        ]);
    }

    /**
     * get an iterator
     *
     * @param  array $items iterator items
     * @return \Iterator
     */
    public function getIteratorMock(array $items)
    {
        $iteratorMock = $this->getMockBuilder(\Iterator::class)->setMethods([
            'rewind',
            'valid',
            'current',
            'key',
            'next',
        ])->getMockForAbstractClass();
        $this->mockIteratorItems($iteratorMock, $items);

        return $iteratorMock;
    }


    /**
     * add items to the iterator
     *
     * @param  \Iterator $iterator
     * @param  array     $items iterator items
     * @param  boolean   $includeCallsToKey
     */
    public function mockIteratorItems(\Iterator $iterator, array $items, $includeCallsToKey = false)
    {
        $iterator->expects($this->at(0))->method('rewind');
        $counter = 1;
        foreach ($items as $k => $v) {
            $iterator->expects($this->at($counter++))->method('valid')->will($this->returnValue(true));
            $iterator->expects($this->at($counter++))->method('current')->will($this->returnValue($v));
            if ($includeCallsToKey) {
                $iterator->expects($this->at($counter++))->method('key')->will($this->returnValue($k));
            }
            $iterator->expects($this->at($counter++))->method('next');
        }
        $iterator->expects($this->at($counter))->method('valid')->will($this->returnValue(false));
    }
}
