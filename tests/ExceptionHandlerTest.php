<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests;

use Exception;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Http\Exceptions\MaintenanceModeException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Intouch\Newrelic\Newrelic;
use Mockery;
use MyParcelCom\JsonApi\ExceptionHandler;
use MyParcelCom\JsonApi\Exceptions\AbstractException;
use MyParcelCom\JsonApi\Exceptions\NotFoundException;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class ExceptionHandlerTest extends TestCase
{
    /** @var ExceptionHandler */
    protected $handler;

    /** @var Request */
    protected $request;

    /** @var string */
    protected $appName = 'Test app';

    protected function setUp()
    {
        parent::setUp();

        $this->request = Mockery::mock(Request::class);

        $factory = Mockery::mock(ResponseFactory::class);
        $factory->shouldReceive('json')
            ->andReturnUsing([$this, 'mockResponse']);

        $this->handler = (new ExceptionHandler(Mockery::mock(Container::class)))
            ->setResponseFactory($factory)
            ->setAppName($this->appName);
    }

    protected function tearDown()
    {
        parent::tearDown();

        Mockery::close();
    }

    /** @test */
    public function testRender()
    {
        // Test rendering a normal exception
        $exception = Mockery::mock(Exception::class);
        $response = $this->handler->render($this->request, $exception);

        $this->assertEquals(500, $response->getStatus(), 'Normal exceptions should produce a 500 http status code');
        $this->checkJson($response->getData());

        // Test rendering a JsonApiException
        $jsonApiException = Mockery::mock(AbstractException::class, [
            'getId'        => 'id',
            'getLinks'     => ['about' => 'some-link'],
            'getStatus'    => 404,
            'getErrorCode' => 8008,
            'getTitle'     => 'You went somewhere that doesn\'t exist',
            'getDetail'    => 'Don\'t pretend like you didn\'t know what you were doing!',
            'getSource'    => ['pointer' => '/data/attributes/some-attribute', 'parameter' => 'some-query-param'],
            'getMeta'      => [
                'non-standard' => 'something non standard can be in here',
            ],
        ]);
        $response = $this->handler->render($this->request, $jsonApiException);

        $this->assertEquals(404, $response->getStatus(), 'Normal exceptions should produce a 500 http status code');
        $this->checkJson($response->getData());
    }

    /**
     * When debug is set to true, the render() method should produce more metadata in the response.
     *
     * @test
     */
    public function testSetDebug()
    {
        $exception = Mockery::mock(Exception::class);

        $this->handler->setDebug(false);
        $standardResponse = $this->handler->render($this->request, $exception);
        $standardError = reset($standardResponse->getData()['errors']);

        $this->handler->setDebug(true);
        $debugResponse = $this->handler->render($this->request, $exception);
        $debugError = reset($debugResponse->getData()['errors']);

        $this->assertArrayHasKey('meta', $debugError, 'Debug error should have meta information');

        $debugData = array_diff_key($debugError['meta'], $standardError['meta'] ?? []);
        $this->assertNotEmpty($debugData, 'Debug error should contain more metadata than standard error');
    }

    /**
     * When contact link is set, the response should contain a contact link.
     *
     * @test
     */
    public function testSetContactLink()
    {
        $exception = Mockery::mock(Exception::class);
        $response = $this->handler->render($this->request, $exception);
        if (isset($response->getData()['links'])) {
            $this->assertArrayNotHasKey('contact', $response->getData()['links'], 'Json error response should not contain a contact link when no contact link is set');
        }

        $this->handler->setContactLink('test@myparcel.com');
        $response = $this->handler->render($this->request, $exception);
        $error = reset($response->getData()['errors']);
        $this->assertArrayHasKey('links', $error, 'Json error should have links when contact link is set');
        $this->assertArrayHasKey('contact', $error['links'], 'Json error should have contact link when contact link is set');
    }

    /**
     * Test whether reporting works.
     *
     * @test
     */
    public function testReport()
    {
        $message = 'an error occured';
        $exception = new Exception($message);
        $trace = $exception->getTrace();

        try {
            $this->handler->report($exception);
        } catch (Throwable $e) {
            $this->fail('An exception was thrown when report was called without a logger');
        }

        $logger = Mockery::mock(LoggerInterface::class);
        $logger->shouldReceive('error')->withArgs([$message, $trace]);

        try {
            $this->handler->setLogger($logger);
            $this->handler->report($exception);
        } catch (Throwable $e) {
            $this->fail('An exception was thrown when report was called with a logger');
        }

        // Mockery didn't throw any errors, so the test succeeded.
        $this->assertTrue(true);
    }

    /** @test */
    public function testNewrelic()
    {
        $exception = new Exception('moar errors occured');

        $newRelic = Mockery::mock(Newrelic::class);
        $newRelic->shouldReceive('noticeError')->andReturnUsing(function ($value) use ($exception) {
            $this->assertEquals($exception->getMessage(), $value);
        });

        $logger = Mockery::mock(LoggerInterface::class);
        $logger->shouldReceive('error')->withArgs([$exception->getMessage(), $exception->getTrace()]);

        $this->handler->setNewrelic($newRelic)->setLogger($logger)->report($exception);
    }

    /**
     * Test whether a correct response is generated when a MaintenanceModeException is handled.
     *
     * @test
     */
    public function testMaintenanceModeHandling()
    {
        // Can't mock exceptions, they have final methods.
        $exception = Mockery::mock(MaintenanceModeException::class);
        $request = Mockery::mock(Request::class, ['path' => '/']);
        $response = $this->handler->render($request, $exception);
        $json = $response->getData();

        $this->assertArrayHasKey('title', $json, 'When in maintenance mode response on \'/\' does not contain a title');
        $this->assertEquals($this->appName, $json['title'], 'When in maintenance mode response on \'/\' contains an incorrect title');

        $this->assertArrayHasKey('status', $json, 'When in maintenance mode response on \'/\' does not contain a status');
        $this->assertEquals(503, $response->getStatus(), 'Maintenance status code was not 503');

        $request = Mockery::mock(Request::class, ['path' => '/some/other/path']);
        $response = $this->handler->render($request, $exception);
        $json = $response->getData();

        $this->checkJson($json);
        $this->assertEquals(503, $response->getStatus(), 'Maintenance status code was not 503');
        $this->assertEquals('503', reset($json['errors'])['status'], 'Maintenance status code was not 503');
    }

    /** @test */
    public function testNotFoundException()
    {
        $exception = Mockery::mock(NotFoundHttpException::class);
        $response = $this->handler->setDebug(true)->render($this->request, $exception);
        $json = $response->getData();

        $this->assertEquals(NotFoundException::class, $json['errors'][0]['meta']['debug']['exception']);
        $this->assertEquals(404, $response->getStatus());
    }

    /**
     * Check if the json array is a valid jsonapi response.
     *
     * @param array $json
     */
    private function checkJson(array $json)
    {
        $this->assertArrayNotHasKey('data', $json, 'Json error response should not contain data');
        $this->assertArrayNotHasKey('included', $json, 'Json error response should not contain included');

        $this->assertArrayHasKey('errors', $json, 'Json error response should contain errors array');
        $this->assertNotEmpty($json['errors'], 'Json error response should contain errors');

        $validRootKeys = ['errors', 'meta', 'jsonapi', 'links'];
        $invalidRootKeys = array_diff(array_keys($json), $validRootKeys);
        $this->assertEmpty($invalidRootKeys, 'Json error reponse contained invalid root keys: ' . implode(', ', $invalidRootKeys));

        foreach ($json['errors'] as $error) {
            $this->assertNotEmpty($error, 'Error should contain information relating to the error');
            $this->assertArrayHasKey('code', $error, 'Error should contain an internal error code');
            $this->assertArrayHasKey('status', $error, 'Error should contain an http status code');

            $validErrorKeys = ['id', 'links', 'status', 'code', 'title', 'detail', 'source', 'meta'];
            $invalidErrorKeys = array_diff(array_keys($error), $validErrorKeys);
            $this->assertEmpty($invalidErrorKeys, 'Error contained invalid keys: ' . implode(', ', $invalidErrorKeys));
        }
    }

    /**
     * Create a mocked JsonResponse with exposed properties.
     *
     * @param array $data
     * @param int   $code
     * @return JsonResponse
     */
    public function mockResponse(array $data, int $code)
    {
        return new class($data, $code) extends JsonResponse
        {
            protected $data;
            protected $status;
            public $headers;
            protected $options;

            public function __construct($data = null, $status = 200, array $headers = [], $options = 0)
            {
                $this->data = $data;
                $this->status = $status;
                $this->headers = $headers;
                $this->options = $options;
            }

            public function getData($assoc = false, $depth = 512)
            {
                return $this->data;
            }

            public function getStatus()
            {
                return $this->status;
            }

            public function getOptions()
            {
                return $this->options;
            }
        };
    }
}
