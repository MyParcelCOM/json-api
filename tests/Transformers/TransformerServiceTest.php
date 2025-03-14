<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Transformers;

use Illuminate\Support\Collection;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use MyParcelCom\JsonApi\Http\Paginator;
use MyParcelCom\JsonApi\Resources\Interfaces\ResourcesInterface;
use MyParcelCom\JsonApi\Tests\Mocks\Resources\FatherMock;
use MyParcelCom\JsonApi\Tests\Mocks\Resources\MotherMock;
use MyParcelCom\JsonApi\Tests\Mocks\Resources\PersonMock;
use MyParcelCom\JsonApi\Tests\Mocks\Transformers\FatherTransformerMock;
use MyParcelCom\JsonApi\Tests\Mocks\Transformers\MotherTransformerMock;
use MyParcelCom\JsonApi\Tests\Mocks\Transformers\PersonTransformerMock;
use MyParcelCom\JsonApi\Transformers\TransformerException;
use MyParcelCom\JsonApi\Transformers\TransformerFactory;
use MyParcelCom\JsonApi\Transformers\TransformerService;
use PHPUnit\Framework\TestCase;

class TransformerServiceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    protected TransformerService $transformerService;

    protected function setUp(): void
    {
        parent::setUp();

        $paginator = Mockery::mock(Paginator::class, [
            'getTotal'   => 3,
            'getStart'   => 0,
            'getPerPage' => 1,
            'getCount'   => 2,
            'getLinks'   => ['self' => 'me'],
        ]);
        $paginator->shouldReceive('addTotal')->andReturnSelf();

        $transformerFactory = (new TransformerFactory())->setMapping([
            PersonMock::class => PersonTransformerMock::class,
            MotherMock::class => MotherTransformerMock::class,
            FatherMock::class => FatherTransformerMock::class,
        ]);

        $this->transformerService = new TransformerService($transformerFactory);
        $this->transformerService->setPaginator($paginator);
        $this->transformerService->setIncludes([
            0        => 'mother',
            1        => 'father',
            'father' => [
                'father' => [
                    'father',
                ],
            ],
        ]);
    }

    public function testSetMaxPageSize(): void
    {
        $paginator = Mockery::mock(Paginator::class);
        $paginator->shouldReceive('setMaxPageSize')->andReturnUsing(function ($maxPageSize) use ($paginator) {
            $this->assertEquals(3, $maxPageSize);

            return $paginator;
        });

        $transformerService = new TransformerService(new TransformerFactory());
        $transformerService->setPaginator($paginator);
        $transformerService->setMaxPageSize(3);
    }

    public function testTransformEmptyResources(): void
    {
        $resources = Mockery::mock(ResourcesInterface::class, [
            'count' => 0,
            'get'   => new Collection([]),
        ]);
        $resources->shouldReceive('limit')->andReturnSelf();
        $resources->shouldReceive('offset')->andReturnSelf();

        $this->assertEquals(
            [
                'data'  => [],
                'meta'  => [
                    'total_pages'   => 2,
                    'total_records' => 3,
                ],
                'links' => [
                    'self' => 'me',
                ],
            ],
            $this->transformerService->transformResources($resources),
        );
    }

    public function testTransformResource(): void
    {
        $resource = new PersonMock('1');

        $this->assertEquals(
            [
                'data'     => [
                    'id'   => '1',
                    'type' => 'person',
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
                ],
            ],
            $this->transformerService->transformResource($resource),
        );
    }

    public function testTransformResourceException(): void
    {
        $this->expectException(TransformerException::class);
        $this->transformerService->transformResource(null);
    }

    public function testTransformResources(): void
    {
        $resources = Mockery::mock(ResourcesInterface::class, [
            'count' => 2,
            'get'   => new Collection([
                new PersonMock('1'),
                new PersonMock('3'),
            ]),
        ]);
        $resources->shouldReceive('limit')->andReturnSelf();
        $resources->shouldReceive('offset')->andReturnSelf();

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
            $this->transformerService->transformResources($resources),
        );
    }
}
