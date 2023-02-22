<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Transformers;

use Mockery;
use MyParcelCom\JsonApi\Http\Paginator;
use MyParcelCom\JsonApi\Transformers\TransformerException;
use MyParcelCom\JsonApi\Transformers\TransformerItem;
use MyParcelCom\JsonApi\Transformers\TransformerResource;
use PHPUnit\Framework\TestCase;

class TransformerResourceTest extends TestCase
{
    use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /** @var TransformerResource */
    protected $transformerResource;

    /** @var  TransformerResource */
    protected $transformerResourceNoIncluded;

    /** @var array */
    protected $included;

    /** @var array */
    protected $data;

    /** @var Paginator */
    protected $paginator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->included = [
            'bar',
        ];
        $this->data = [
            'relationships' => [
                'foo' => 'bar',
            ],
        ];
        $transformerItem = Mockery::mock(TransformerItem::class, [
            'getData'     => [$this->data],
            'getIncluded' => $this->included,
        ]);
        $transformerItemNoIncluded = Mockery::mock(TransformerItem::class, [
            'getData'     => [$this->data],
            'getIncluded' => [],
        ]);

        $this->transformerResource = new TransformerResource([$transformerItem]);
        $this->transformerResourceNoIncluded = new TransformerResource([$transformerItemNoIncluded]);
        $this->paginator = Mockery::mock(Paginator::class, [
            'getCount' => 0,
            'getLinks' => [],
        ]);
    }

    /** @test */
    public function testGetData()
    {
        $expectedResult = [
            'data'     => [$this->data],
            'included' => $this->included,
        ];

        $this->transformerResource->setPaginator($this->paginator);

        $this->assertEquals($expectedResult, $this->transformerResource->GetData());
    }

    /** @test */
    public function testGetDataEmpty()
    {
        $expectedResult = [
            'data' => [],
        ];

        $emptyTransformerResource = new TransformerResource([]);
        $emptyTransformerResource->setPaginator($this->paginator);

        $this->assertEquals($expectedResult, $emptyTransformerResource->GetData());
    }

    /** @test */
    public function testGetDataWithNoIncludes()
    {
        $expectedResult = [
            'data' => [$this->data],
        ];

        $this->transformerResourceNoIncluded->setRequestedIncludes(['foo']);
        $this->transformerResourceNoIncluded->setPaginator($this->paginator);

        $this->assertEquals($expectedResult, $this->transformerResourceNoIncluded->GetData());
    }

    /** @test */
    public function testGetDataWithNoPaginator()
    {
        $expectedResult = [
            'data'     => [$this->data],
            'meta'     => ['total_pages' => 0],
            'included' => $this->included,
        ];

        $this->expectException(TransformerException::class);
        $this->assertEquals($expectedResult, $this->transformerResource->toArrayMultiple());
    }

    /** @test */
    public function testAddMeta()
    {
        $this->transformerResource->addMeta(['x' => 'y']);
        $this->assertEquals([
            'data' => [],
            'meta' => ['x' => 'y'],
        ], $this->transformerResource->toArraySingle());
    }

    /** @test */
    public function testAddMetaException()
    {
        $this->expectException(TransformerException::class);
        $this->transformerResource->addMeta((object) []);
    }
}
