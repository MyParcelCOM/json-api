<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Http\Traits;

use MyParcelCom\JsonApi\Http\Interfaces\RequestInterface;
use MyParcelCom\JsonApi\Http\Paginator;
use MyParcelCom\JsonApi\Http\Traits\RequestTrait;
use PHPUnit\Framework\TestCase;

class RequestTraitTest extends TestCase
{
    /** @test */
    public function testGetPaginator()
    {
        $request = $this->createRequestTraitMock();

        $paginator = $request->getPaginator();
        $this->assertInstanceOf(
            Paginator::class,
            $paginator
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
            $paginator->getLinks()
        );
    }

    /** @test */
    public function testGetIncludes()
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
            $request->getIncludes()
        );
    }

    /** @test */
    public function testGetSort()
    {
        $request = $this->createRequestTraitMock();
        $this->assertEquals(
            [
                'this' => 'DESC',
                'that' => 'ASC',
            ],
            $request->getSort()
        );
    }

    /** @test */
    public function testGetFilter()
    {
        $request = $this->createRequestTraitMock();
        $this->assertEquals(
            [
                'foo' => 'bar',
                'yes' => [
                    'some' => 'thing',
                ],
            ],
            $request->getFilter()
        );
    }

    /**
     * @return RequestInterface
     */
    private function createRequestTraitMock()
    {
        return new class implements RequestInterface {
            use RequestTrait;

            private $data = [
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

            public function query($key = null, $default = null)
            {
                return $this->data[$key] ?? $default;
            }

            /**
             * Get the full URL for the request.
             *
             * @return string
             */
            public function fullUrl(): string
            {
                return 'https://some.url.com/resource?page[size]=5&page[number]=3&include=all,the,things';
            }
        };
    }
}
