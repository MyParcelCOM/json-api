<?php declare(strict_types=1);

namespace MyParcelCom\Transformers\Tests;

use Illuminate\Support\Collection;
use Mockery;
use MyParcelCom\Common\Contracts\JsonApiRequestInterface;
use MyParcelCom\Common\Contracts\ResultSetInterface;
use MyParcelCom\Common\Http\Paginator;
use MyParcelCom\Transformers\Tests\Stubs\ResourcesInterfaceStub;
use MyParcelCom\Transformers\TransformerCollection;
use MyParcelCom\Transformers\TransformerFactory;
use MyParcelCom\Transformers\TransformerItem;
use MyParcelCom\Transformers\TransformerResource;
use MyParcelCom\Transformers\TransformerService;
use PHPUnit\Framework\TestCase;

class TransformerServiceTest extends TestCase
{
    /** @var TransformerService */
    protected $transformerService;
    /** @var ResultSetInterface */
    protected $resources;

    /**
     * setup test conditions
     */
    public function setUp()
    {
        parent::setUp();

        $paginator = Mockery::mock(Paginator::class, [
            'getTotal'   => 3,
            'getStart'   => 0,
            'getPerPage' => 1,
            'addTotal'   => 2,
            'getCount'   => 2,
            'getLinks'   => [],
        ]);
        $request = Mockery::mock(JsonApiRequestInterface::class, ['getPaginator' => $paginator, 'getIncludes' => []]);

        $this->resources = Mockery::mock(ResultSetInterface::class, [
            'count' => 0,
            'get'   => Mockery::mock(Collection::class, ['count' => 0]),
        ]);
        $this->resources->shouldReceive('limit')->andReturnSelf();
        $this->resources->shouldReceive('offset')->andReturnSelf();

        $transformerFactory = Mockery::mock(TransformerFactory::class, [
            'createTransformerCollection' => Mockery::mock(TransformerCollection::class),
            'createTransformerItem'       => Mockery::mock(TransformerItem::class),
        ]);

        $this->transformerService = new TransformerService($request, $transformerFactory);
    }

    /**
     * tearDown test conditions
     */
    public function tearDown()
    {
        parent::tearDown();
        Mockery::close();
    }

    /**
     * test TransformResultSets function
     */
    public function testTransformResultSets()
    {
        $result = $this->transformerService->transformResources($this->resources);
        $this->assertInternalType('array', $result);
    }
}
