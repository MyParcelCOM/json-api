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

    /**
     * setup test conditions
     */
    public function setUp()
    {
        parent::setUp();
        $this->included = ['foo' => 'bar'];
        $this->data = ['relationships' => ['foo' => 'bar']];
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
        $this->paginator = Mockery::mock(Paginator::class, ['getCount' => 0, 'getLinks' => []]);
    }

    /**
     * tear down test conditions
     */
    public function tearDown()
    {
        parent::tearDown();
        Mockery::close();
    }

    /**
     * @test
     */
    public function testToArray()
    {
        $expectedResult = ["data" => [$this->data], "meta" => ["total_pages" => 0], 'includes' => $this->included];

        $this->transformerResource->setPaginator($this->paginator);

        $this->assertEquals($expectedResult, $this->transformerResource->toArray());
    }

    /**
     * test ToArray function with no data
     *
     * @test
     */
    public function testToArrayEmpty()
    {
        $expectedResult = ["data" => [], "meta" => ["total_pages" => 0]];

        $emptyTransformerResource = new TransformerResource([]);
        $emptyTransformerResource->setPaginator($this->paginator);

        $this->assertEquals($expectedResult, $emptyTransformerResource->toArray());
    }


    /**
     * test ToArray function with no includes
     */
    public function testToArrayWithNoIncludes()
    {
        $expectedResult = ["data" => [$this->data], "meta" => ["total_pages" => 0]];

        $this->transformerResourceNoIncluded->setRequestedIncludes(['foo']);
        $this->transformerResourceNoIncluded->setPaginator($this->paginator);

        $this->assertEquals($expectedResult, $this->transformerResourceNoIncluded->toArray());
    }

    /**
     * test ToArray function wit no paginator
     */
    public function testToArrayWithNoPaginator()
    {
        $this->expectException(TransformerException::class);

        $expectedResult = ["data" => [$this->data], "meta" => ["total_pages" => 0], 'includes' => $this->included];

        $this->assertEquals($expectedResult, $this->transformerResource->toArray());
    }
}
