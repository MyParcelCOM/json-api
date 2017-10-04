<?php declare(strict_types=1);

namespace MyParcelCom\Common\Tests\Http;

use MyParcelCom\Common\Contracts\JsonApiRequestInterface;
use MyParcelCom\Common\Http\Paginator;
use MyParcelCom\Common\Traits\JsonApiRequestTrait;
use PHPUnit\Framework\TestCase;

class JsonApiRequestTraitTest extends TestCase
{
    /** @test */
    public function testGetPaginator()
    {
        $request = $this->createJsonApiRequestTraitMock();

        $paginator = $request->getPaginator();
        $this->assertInstanceOf(
            Paginator::class,
            $paginator
        );

        $paginator->setTotal(25);
        $this->assertEquals(
            $paginator->getLinks(),
            [
                'self'  => 'https://some.url.com/resource?page[size]=5&page[number]=3&includes=all,the,things',
                'next'  => 'https://some.url.com/resource?page[size]=5&page[number]=4&includes=all,the,things',
                'prev'  => 'https://some.url.com/resource?page[size]=5&page[number]=2&includes=all,the,things',
                'last'  => 'https://some.url.com/resource?page[size]=5&page[number]=5&includes=all,the,things',
                'first' => 'https://some.url.com/resource?page[size]=5&page[number]=1&includes=all,the,things',
            ]
        );
    }

    /** @test */
    public function testGetIncludes()
    {
        $request = $this->createJsonApiRequestTraitMock();
        $this->assertEquals(
            [
                'all',
                'the',
                'things',
            ],
            $request->getIncludes()
        );
    }

    /** @test */
    public function testGetSort()
    {
        $request = $this->createJsonApiRequestTraitMock();
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
        $request = $this->createJsonApiRequestTraitMock();
        $this->assertEquals(
            [
                'foo' => 'bar',
            ],
            $request->getFilter()
        );
    }

    /**
     * @return JsonApiRequestInterface
     */
    private function createJsonApiRequestTraitMock()
    {
        return new class implements JsonApiRequestInterface
        {
            use JsonApiRequestTrait;

            private $data = [
                'includes' => 'all,the,things',
                'page'     => [
                    'number' => 3,
                    'size'   => 5,
                ],
                'sort'     => '-this,that',
                'filter'   => [
                    'foo' => 'bar',
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
            public function fullUrl()
            {
                return 'https://some.url.com/resource?page[size]=5&page[number]=3&includes=all,the,things';
            }
        };
    }
}
