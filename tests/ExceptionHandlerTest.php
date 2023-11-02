<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests;

use Exception;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Mockery;
use MyParcelCom\JsonApi\ExceptionHandler;
use MyParcelCom\JsonApi\Exceptions\AbstractException;
use MyParcelCom\JsonApi\Exceptions\AbstractMultiErrorException;
use MyParcelCom\JsonApi\Exceptions\CarrierApiException;
use MyParcelCom\JsonApi\Exceptions\GenericCarrierException;
use MyParcelCom\JsonApi\Exceptions\Interfaces\ExceptionInterface;
use MyParcelCom\JsonApi\Exceptions\MethodNotAllowedException;
use MyParcelCom\JsonApi\Exceptions\NotFoundException;
use MyParcelCom\JsonApi\Exceptions\TooManyRequestsException;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class ExceptionHandlerTest extends TestCase
{
    use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    protected ExceptionHandler $handler;

    protected Request $request;

    protected string $appName = 'Test app';

    protected function setUp(): void
    {
        parent::setUp();

        $this->request = Mockery::mock(Request::class, [
            'getMethod' => 'GET',
            'post'      => [],
        ]);

        $factory = Mockery::mock(ResponseFactory::class);
        $factory->shouldReceive('json')
            ->andReturnUsing([$this, 'mockResponse']);

        $this->handler = (new ExceptionHandler(Mockery::mock(Container::class, ['get' => $this->request])))
            ->setResponseFactory($factory)
            ->setAppName($this->appName);
    }

    /** @test */
    public function testRenderNormalException()
    {
        $exception = Mockery::mock(Exception::class);
        $response = $this->handler->render($this->request, $exception);

        $this->assertEquals(500, $response->getStatus(), 'Normal exceptions should produce a 500 http status code');
        $this->checkJson($response->getData());
    }

    private function createExceptionMock()
    {
        return Mockery::mock(AbstractException::class, [
            'getId'        => 'id',
            'getLinks'     => ['about' => 'some-link'],
            'getStatus'    => 404,
            'getErrorCode' => 8008,
            'getTitle'     => 'You went somewhere that doesn\'t exist',
            'getDetail'    => 'Don\'t pretend like you didn\'t know what you were doing!',
            'getSource'    => [
                'pointer'   => '/data/attributes/some-attribute',
                'parameter' => 'some-query-param',
            ],
            'getMeta'      => [
                'non-standard' => 'something non standard can be in here',
            ],
        ]);
    }

    /** @test */
    public function testRenderJsonApiException()
    {
        $response = $this->handler->render($this->request, $this->createExceptionMock());

        $this->assertEquals(404, $response->getStatus());
        $this->checkJson($response->getData());
    }

    /** @test */
    public function testRenderValidationException()
    {
        $exception = Mockery::mock(ValidationException::class, [
            'errors' => [
                'data.attributes.some-attribute' => [
                    'Something wrong with data.attributes.some-attribute',
                ],
            ],
        ]);
        $response = $this->handler->render($this->request, $exception);

        $this->assertEquals(422, $response->getStatus());
        $this->assertEquals('Something wrong with some-attribute', Arr::get($response->getData(), 'errors.0.detail'));
    }

    /** @test */
    public function testRenderMultiErrorException()
    {
        $exception = Mockery::mock(AbstractMultiErrorException::class, [
            'getErrors' => [
                $this->createExceptionMock(),
            ],
            'getMeta'   => ['teapot'],
            'getStatus' => 418,
        ]);
        $response = $this->handler->render($this->request, $exception);

        $this->assertEquals(418, $response->getStatus());
        $this->checkJson($response->getData());
    }

    /** @test */
    public function testSetDebugShouldRenderMetaData()
    {
        $exception = new Exception();

        $this->handler->setDebug(false);
        $standardResponse = $this->handler->render($this->request, $exception);
        $standardError = reset($standardResponse->getData()['errors']);

        $this->handler->setDebug(true);
        $debugResponse = $this->handler->render($this->request, $exception);
        $debugError = reset($debugResponse->getData()['errors']);

        $this->assertArrayHasKey('meta', $debugError, 'Debug error should have meta information');

        $debugData = array_diff_key($debugError['meta'], $standardError['meta'] ?? []);
        $this->assertNotEmpty($debugData, 'Debug error should contain more metadata than standard error');

        $multiException = new GenericCarrierException([], 0);
        $debugResponse = $this->handler->render($this->request, $multiException);

        $this->assertArrayHasKey('meta', $debugResponse->getData(), 'Response should have meta information');
    }

    /** @test */
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

    /** @test */
    public function testReport()
    {
        $exception = new Exception('an error occurred');

        try {
            $this->handler->report($exception);
        } catch (Throwable) {
            $this->fail('An exception was thrown when report was called without a logger');
        }

        $logger = Mockery::mock(LoggerInterface::class);
        $logger->shouldReceive('error')->andReturnUsing(function ($message, $context) use ($exception) {
            $this->assertEquals('an error occurred', $message);
            $this->assertEquals([
                'trace' => array_slice($exception->getTrace(), 0, 5),
                'file'  => $exception->getFile(),
                'line'  => $exception->getLine(),
            ], $context);
        });

        try {
            $this->handler->setLogger($logger);
            $this->handler->report($exception);
        } catch (Throwable) {
            $this->fail('An exception was thrown when report was called with a logger');
        }
    }

    /** @test */
    public function testReportMultiErrorException()
    {
        $exception = Mockery::mock(AbstractMultiErrorException::class, [
            'getErrors' => [
                $this->createExceptionMock(),
                $this->createExceptionMock(),
            ],
        ]);
        $logger = Mockery::mock(LoggerInterface::class);
        $logger->shouldReceive('error')->twice()->andReturnUsing(function ($message, $context) {
            $this->assertEquals('You went somewhere that doesn\'t exist', $message);
            $this->assertEquals('8008', $context['code']);
            $this->assertEquals('Don\'t pretend like you didn\'t know what you were doing!', $context['detail']);
            $this->assertEquals([
                'pointer'   => '/data/attributes/some-attribute',
                'parameter' => 'some-query-param',
            ], $context['source']);
        });

        $this->handler->setLogger($logger)->report($exception);
    }

    /** @test */
    public function testReportShouldLogWarningsForStatusCodeBelow500()
    {
        $exception = new CarrierApiException(422, ['nono' => 'not good']);
        $trace = array_slice($exception->getTrace(), 0, 5);

        try {
            $this->handler->report($exception);
        } catch (Throwable) {
            $this->fail('An exception was thrown when report was called without a logger');
        }

        $logger = Mockery::mock(LoggerInterface::class);
        $logger->shouldReceive('warning')->withArgs([
            "There was a problem with the request to the carrier. The original response can be found in the meta under `carrier_response`.",
            [
                'trace' => $trace,
                'file'  => $exception->getFile(),
                'line'  => $exception->getLine(),
            ],
        ]);

        try {
            $this->handler->setLogger($logger);
            $this->handler->report($exception);
        } catch (Throwable) {
            $this->fail('An exception was thrown when report was called with a logger');
        }

        $this->assertTrue(true);
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

    /** @test */
    public function testItMapsAMethodNotAllowedHttpExceptionToAMethodNotAllowedException()
    {
        $exception = Mockery::mock(MethodNotAllowedHttpException::class);
        $response = $this->handler->setDebug(true)->render($this->request, $exception);
        $responseData = $response->getData();

        $this->assertNotEmpty($responseData['errors']);
        $this->assertEquals(MethodNotAllowedException::class, $responseData['errors'][0]['meta']['debug']['exception']);
        $this->assertEquals(405, $response->getStatus());
        $this->assertEquals(405, $responseData['errors'][0]['status']);
        $this->assertEquals(10009, $responseData['errors'][0]['code']);
        $this->assertEquals('Method not allowed', $responseData['errors'][0]['title']);
        $this->assertEquals("The 'GET' method is not allowed on this endpoint.", $responseData['errors'][0]['detail']);
    }

    /** @test */
    public function testItMapsThrottleExceptionsToTooManyRequestsException()
    {
        $exception = Mockery::mock(ThrottleRequestsException::class);
        $response = $this->handler->setDebug(true)->render($this->request, $exception);
        $json = $response->getData();

        $this->assertEquals(TooManyRequestsException::class, $json['errors'][0]['meta']['debug']['exception']);
        $this->assertEquals(429, $response->getStatus());
        $this->assertEquals(ExceptionInterface::TOO_MANY_REQUESTS['title'], $json['errors'][0]['title']);
    }

    /** @test */
    public function testItSetsTraceToNoTraceIsAvailableWhenTraceIsInvalidForJsonEncode()
    {
        // The error we encountered was caused when binary data was passed to a method that tried to json_encode the
        // binary data and failed. It would then throw an exception. The exception would have a stack trace and part of
        // the stack trace are the arguments of all the calling methods, which would hold the binary data. Which in turn
        // gets json encoded by the renderer and causes it to fail.
        // To simulate this behaviour, we create a method that throws an exception and pass binary data to it, so the
        // data shows up in the trace array.
        $badMethod = function ($string) {
            json_encode($string);

            throw new InvalidArgumentException(json_last_error_msg());
        };

        try {
            $badMethod(file_get_contents(__DIR__ . '/Stubs/random-pictures.pdf'));
        } catch (InvalidArgumentException $e) {
            $response = $this->handler->setDebug(true)->render($this->request, $e);
            $responseData = $response->getData();
            $this->assertEquals(
                'Trace is not available.',
                $responseData['errors'][0]['meta']['debug']['trace'],
                'Binary data cannot be json encoded and should therefore cause the trace to not be rendered'
            );
        }
    }

    /**
     * Check if the json array is a valid jsonapi response.
     */
    private function checkJson(array $json)
    {
        $this->assertArrayNotHasKey('data', $json, 'Json error response should not contain data');
        $this->assertArrayNotHasKey('included', $json, 'Json error response should not contain included');

        $this->assertArrayHasKey('errors', $json, 'Json error response should contain errors array');
        $this->assertNotEmpty($json['errors'], 'Json error response should contain errors');

        $validRootKeys = ['errors', 'meta', 'jsonapi', 'links'];
        $invalidRootKeys = array_diff(array_keys($json), $validRootKeys);
        $this->assertEmpty($invalidRootKeys, 'Json error response contained invalid root keys: ' . implode(', ', $invalidRootKeys));

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
     */
    public function mockResponse(array $data, int $code): JsonResponse
    {
        return new class ($data, $code) extends JsonResponse {
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
