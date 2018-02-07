<?php declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Transformers;

use Illuminate\Support\Collection;
use Mockery;
use MyParcelCom\JsonApi\Http\Interfaces\RequestInterface;
use MyParcelCom\JsonApi\Http\Paginator;
use MyParcelCom\JsonApi\Interfaces\ResultSetInterface;
use MyParcelCom\JsonApi\Transformers\TransformerCollection;
use MyParcelCom\JsonApi\Transformers\TransformerFactory;
use MyParcelCom\JsonApi\Transformers\TransformerItem;
use MyParcelCom\JsonApi\Transformers\TransformerService;
use PHPUnit\Framework\TestCase;

class TransformerServiceTest extends TestCase
{
    /** @var TransformerService */
    protected $transformerService;

    /** @var ResultSetInterface */
    protected $resources;

    protected function setUp()
    {
        parent::setUp();

        $paginator = Mockery::mock(Paginator::class, [
            'getTotal'   => 3,
            'getStart'   => 0,
            'getPerPage' => 1,
            'addTotal'   => 2,
            'getCount'   => 2,
            'getLinks'   => ['self' => 'me'],
        ]);
        $request = Mockery::mock(RequestInterface::class, [
            'getPaginator' => $paginator,
            'getIncludes'  => [],
        ]);

        $this->resources = Mockery::mock(ResultSetInterface::class, [
            'count' => 0,
            'get'   => Mockery::mock(Collection::class, [
                'count' => 0,
            ]),
        ]);
        $this->resources->shouldReceive('limit')->andReturnSelf();
        $this->resources->shouldReceive('offset')->andReturnSelf();

        $transformerFactory = Mockery::mock(TransformerFactory::class, [
            'createTransformerCollection' => Mockery::mock(TransformerCollection::class),
            'createTransformerItem'       => Mockery::mock(TransformerItem::class),
        ]);

        $this->transformerService = new TransformerService($request, $transformerFactory);
    }

    protected function tearDown()
    {
        parent::tearDown();

        Mockery::close();
    }

    /** @test */
    public function testTransformResultSets()
    {
        $result = $this->transformerService->transformResources($this->resources);
        $this->assertEquals([
            'data'  => [],
            'meta'  => [
                'total_pages'   => 2,
                'total_records' => 3,
            ],
            'links' => [
                'self' => 'me',
            ],
        ], $result);
    }
}
