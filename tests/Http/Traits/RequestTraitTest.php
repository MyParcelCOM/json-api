<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Http\Traits;

use MyParcelCom\JsonApi\Http\Interfaces\RequestInterface;
use MyParcelCom\JsonApi\Http\Paginator;
use MyParcelCom\JsonApi\Http\Traits\RequestTrait;
use PHPUnit\Framework\TestCase;

class RequestTraitTest extends TestCase
{
    public function testGetPaginator(): void
    {
        $request = $this->createRequestTraitMock();

        $paginator = $request->getPaginator();
        $this->assertInstanceOf(
            Paginator::class,
            $paginator,
        );

        $paginator->setTotal(25);
        $this->assertEquals(
            [
                'self'  => 'https://some.url.com/resource?page[size]=5&page[number]=3&include=all,the,things',
                'first' => 'https://some.url.com/resource?page[size]=5&page[number]=1&include=all,the,things',
                'prev'  => 'https://some.url.com/resource?page[size]=5&page[number]=2&include=all,the,things',
                'next'  => 'https://some.url.com/resource?page[size]=5&page[number]=4&include=all,the,things',
                'last'  => 'https://some.url.com/resource?page[size]=5&page[number]=5&include=all,the,things',
            ],
            $paginator->getLinks(),
        );
    }

    public function testGetIncludes(): void
    {
        $request = $this->createRequestTraitMock();
        $this->assertEquals(
            [
                'all',
                'the',
                'things',
                'nested' => [
                    'relationship',
                    'merge',
                ],
                'even'   => [
                    'deeper'  => [
                        'relationship',
                        'merge',
                    ],
                    'nastier' => [
                        'merge',
                    ],
                ],
            ],
            $request->getIncludes(),
        );
    }

    public function testGetSort(): void
    {
        $request = $this->createRequestTraitMock();
        $this->assertEquals(
            [
                'this' => 'DESC',
                'that' => 'ASC',
            ],
            $request->getSort(),
        );
    }

    public function testGetFilter(): void
    {
        $request = $this->createRequestTraitMock();
        $this->assertEquals(
            [
                'foo' => 'bar',
                'yes' => [
                    'some' => 'thing',
                ],
            ],
            $request->getFilter(),
        );
    }

    private function createRequestTraitMock(): RequestInterface
    {
        return new class implements RequestInterface {
            use RequestTrait;

            private array $data = [
                'include' => 'all,the,things,nested.relationship,even.deeper.relationship,nested.merge,even.deeper.merge,even.nastier.merge',
                'page'    => [
                    'number' => 3,
                    'size'   => 5,
                ],
                'sort'    => '-this,that',
                'filter'  => [
                    'foo' => 'bar',
                    'baz' => '',
                    'yes' => [
                        'some' => 'thing',
                    ],
                    'no'  => [
                        'thing' => '',
                    ],
                ],
            ];

            public function query($key = null, $default = null): string|array
            {
                return $this->data[$key] ?? $default;
            }

            public function fullUrl(): string
            {
                return 'https://some.url.com/resource?page[size]=5&page[number]=3&include=all,the,things';
            }
        };
    }
}
