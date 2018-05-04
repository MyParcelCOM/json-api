<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Transformers;

use Illuminate\Support\Collection;
use Mockery;
use MyParcelCom\JsonApi\Http\Interfaces\RequestInterface;
use MyParcelCom\JsonApi\Http\Paginator;
use MyParcelCom\JsonApi\Resources\Interfaces\ResourcesInterface;
use MyParcelCom\JsonApi\Tests\Mocks\Resources\FatherMock;
use MyParcelCom\JsonApi\Tests\Mocks\Resources\MotherMock;
use MyParcelCom\JsonApi\Tests\Mocks\Resources\PersonMock;
use MyParcelCom\JsonApi\Tests\Mocks\Transformers\FatherTransformerMock;
use MyParcelCom\JsonApi\Tests\Mocks\Transformers\MotherTransformerMock;
use MyParcelCom\JsonApi\Tests\Mocks\Transformers\PersonTransformerMock;
use MyParcelCom\JsonApi\Transformers\TransformerFactory;
use MyParcelCom\JsonApi\Transformers\TransformerService;
use PHPUnit\Framework\TestCase;

class TransformerServiceTest extends TestCase
{
    /** @var TransformerService */
    protected $transformerService;

    /** @var ResourcesInterface */
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
            'getIncludes'  => [
                'mother',
                'father',
                'father' => [
                    'father' => [
                        'father',
                    ],
                ],
            ],
        ]);

        $this->resources = Mockery::mock(ResourcesInterface::class, [
            'count' => 2,
            'get'   => new Collection([
                new PersonMock('1'),
                new PersonMock('3'),
            ]),
        ]);
        $this->resources->shouldReceive('limit')->andReturnSelf();
        $this->resources->shouldReceive('offset')->andReturnSelf();

        $transformerFactory = (new TransformerFactory())
            ->setMapping([
                PersonMock::class => PersonTransformerMock::class,
                MotherMock::class => MotherTransformerMock::class,
                FatherMock::class => FatherTransformerMock::class,
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
        $this->assertEquals(
            [
                'data'     => [
                    [
                        'id'   => '1',
                        'type' => 'person',
                    ],
                    [
                        'id'   => '3',
                        'type' => 'person',
                    ],
                ],
                'meta'     => [
                    'total_pages'   => 2,
                    'total_records' => 3,
                ],
                'included' => [
                    [
                        // The first person has an id of 1 and the mock has related models with ids that are 1 higher
                        'id'   => '2',
                        'type' => 'mother',
                    ],
                    [
                        // Same as above
                        'id'   => '2',
                        'type' => 'father',
                    ],
                    [
                        // father.father.father relationship, has id increased by 1 for each deeper relationship
                        'id'   => '4',
                        'type' => 'father',
                    ],
                    [
                        // Mother of person with id 3
                        'id'   => '4',
                        'type' => 'mother',
                    ],
                    [
                        // Father 4 overlaps, so only 6 (father.father.father) is included
                        'id'   => '6',
                        'type' => 'father',
                    ],
                ],
                'links'    => [
                    'self' => 'me',
                ],
            ],
            $result
        );
    }
}
