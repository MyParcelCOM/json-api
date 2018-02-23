<?php declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Mocks;

use Mockery;
use Mockery\Exception;
use MyParcelCom\JsonApi\Tests\Traits\AssertionsTraitTest;
use MyParcelCom\JsonApi\Traits\AssertionsTrait;

class AssertionsMock
{
    use AssertionsTrait;

    /** @var AssertionsTraitTest */
    protected $app;

    public function __construct($appMock)
    {
        $this->app = $appMock;
    }

    public function json($method, $url, $body, $headers)
    {
        $responseMock = Mockery::mock();
        $responseMock->shouldReceive('assertStatus')->withArgs([101]);
        $responseMock->shouldReceive('getContent')->andReturnUsing(function () use ($method, $url, $body, $headers) {
            if (json_encode([$method, $url, $body, $headers]) !== '["GET","human",[],["head"]]') {
                throw new Exception('unexpected json() parameters');
            }

            return '{"data":[404]}';
        });

        return $responseMock;
    }

    public function assertTrue($condition, $message = '')
    {
        $this->app->assertTrue($condition, $message);
    }

    public function assertCount($expectedCount, $haystack, $message = '')
    {
        $this->app->assertCount($expectedCount, $haystack, $message);
    }
}
