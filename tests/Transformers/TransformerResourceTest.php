<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Transformers;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use MyParcelCom\JsonApi\Http\Paginator;
use MyParcelCom\JsonApi\Transformers\TransformerException;
use MyParcelCom\JsonApi\Transformers\TransformerItem;
use MyParcelCom\JsonApi\Transformers\TransformerResource;
use PHPUnit\Framework\TestCase;

class TransformerResourceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    protected TransformerResource $transformerResource;

    protected TransformerResource $transformerResourceNoIncluded;

    protected array $included;

    protected array $data;

    protected Paginator $paginator;

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

    public function testGetData(): void
    {
        $expectedResult = [
            'data'     => [$this->data],
            'included' => $this->included,
        ];

        $this->transformerResource->setPaginator($this->paginator);

        $this->assertEquals($expectedResult, $this->transformerResource->GetData());
    }

    public function testGetDataEmpty(): void
    {
        $expectedResult = [
            'data' => [],
        ];

        $emptyTransformerResource = new TransformerResource([]);
        $emptyTransformerResource->setPaginator($this->paginator);

        $this->assertEquals($expectedResult, $emptyTransformerResource->GetData());
    }

    public function testGetDataWithNoIncludes(): void
    {
        $expectedResult = [
            'data' => [$this->data],
        ];

        $this->transformerResourceNoIncluded->setRequestedIncludes(['foo']);
        $this->transformerResourceNoIncluded->setPaginator($this->paginator);

        $this->assertEquals($expectedResult, $this->transformerResourceNoIncluded->GetData());
    }

    public function testGetDataWithNoPaginator(): void
    {
        $expectedResult = [
            'data'     => [$this->data],
            'meta'     => ['total_pages' => 0],
            'included' => $this->included,
        ];

        $this->expectException(TransformerException::class);
        $this->assertEquals($expectedResult, $this->transformerResource->toArrayMultiple());
    }

    public function testAddMeta(): void
    {
        $this->transformerResource->addMeta(['x' => 'y']);
        $this->assertEquals([
            'data' => [],
            'meta' => ['x' => 'y'],
        ], $this->transformerResource->toArraySingle());
    }

    public function testAddMetaException(): void
    {
        $this->expectException(TransformerException::class);
        $this->transformerResource->addMeta((object) []);
    }
}
