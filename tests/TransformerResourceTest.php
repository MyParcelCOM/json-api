<?php declare(strict_types=1);

namespace MyParcelCom\Transformers\Tests;

use Mockery;
use MyParcelCom\Common\Http\Paginator;
use MyParcelCom\Transformers\TransformerException;
use MyParcelCom\Transformers\TransformerItem;
use MyParcelCom\Transformers\TransformerResource;
use PHPUnit\Framework\TestCase;

class TransformerResourceTest extends TestCase
{
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

    public function setUp()
    {
        parent::setUp();

        $this->included = [
            'foo' => 'bar',
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

    public function tearDown()
    {
        parent::tearDown();
        Mockery::close();
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
}
