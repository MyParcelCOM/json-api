<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi;

use Exception;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Foundation\Http\Exceptions\MaintenanceModeException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use MyParcelCom\JsonApi\Exceptions\AuthException;
use MyParcelCom\JsonApi\Exceptions\CarrierDataNotFoundException;
use MyParcelCom\JsonApi\Exceptions\Interfaces\ExceptionInterface;
use MyParcelCom\JsonApi\Exceptions\Interfaces\JsonSchemaErrorInterface;
use MyParcelCom\JsonApi\Exceptions\Interfaces\MultiErrorInterface;
use MyParcelCom\JsonApi\Exceptions\InvalidAccessTokenException;
use MyParcelCom\JsonApi\Exceptions\InvalidClientException;
use MyParcelCom\JsonApi\Exceptions\InvalidInputException;
use MyParcelCom\JsonApi\Exceptions\InvalidJsonSchemaException;
use MyParcelCom\JsonApi\Exceptions\InvalidScopeException;
use MyParcelCom\JsonApi\Exceptions\InvalidSecretException;
use MyParcelCom\JsonApi\Exceptions\MethodNotAllowedException;
use MyParcelCom\JsonApi\Exceptions\MissingScopeException;
use MyParcelCom\JsonApi\Exceptions\MissingTokenException;
use MyParcelCom\JsonApi\Exceptions\NotFoundException;
use MyParcelCom\JsonApi\Exceptions\ResourceCannotBeModifiedException;
use MyParcelCom\JsonApi\Exceptions\ResourceNotFoundException;
use MyParcelCom\JsonApi\Exceptions\TooManyRequestsException;
use MyParcelCom\JsonApi\Exceptions\UnprocessableEntityException;
use MyParcelCom\JsonApi\Transformers\ErrorTransformer;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Exception\RequestExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class ExceptionHandler extends Handler
{
    /** @var ResponseFactory */
    protected $responseFactory;

    /** @var bool */
    protected $debug;

    /** @var string */
    protected $contactLink;

    /** @var LoggerInterface */
    protected $logger;

    /** @var string */
    protected $appName;

    protected $dontReport = [
        AuthException::class,
        CarrierDataNotFoundException::class,
        InvalidAccessTokenException::class,
        InvalidClientException::class,
        InvalidInputException::class,
        InvalidJsonSchemaException::class,
        InvalidScopeException::class,
        InvalidSecretException::class,
        MethodNotAllowedException::class,
        MissingScopeException::class,
        MissingTokenException::class,
        NotFoundException::class,
        ResourceCannotBeModifiedException::class,
        ResourceNotFoundException::class,
        UnprocessableEntityException::class,
    ];

    /**
     * Set the Response Factory.
     *
     * @param ResponseFactory $factory
     * @return $this
     */
    public function setResponseFactory(ResponseFactory $factory): self
    {
        $this->responseFactory = $factory;

        return $this;
    }

    /**
     * Set the debug value.
     *
     * @param bool $debug
     * @return $this
     */
    public function setDebug(bool $debug): self
    {
        $this->debug = $debug;

        return $this;
    }

    /**
     * Set the contact link.
     *
     * @param string $link
     * @return $this
     */
    public function setContactLink(string $link): self
    {
        $this->contactLink = $link;

        return $this;
    }

    /**
     * Set the logger for error reporting.
     *
     * @param LoggerInterface $logger
     * @return $this
     */
    public function setLogger(LoggerInterface $logger): self
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * Set the name of the running app.
     *
     * @param string $appName
     * @return $this
     */
    public function setAppName(string $appName)
    {
        $this->appName = $appName;

        return $this;
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request   $request
     * @param Exception $exception
     * @return JsonResponse
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof MaintenanceModeException) {
            return $this->responseFactory->json(
                $this->getMaintenanceJsonResponse($exception, $request),
                Response::HTTP_SERVICE_UNAVAILABLE
            );
        }

        if ($exception instanceof NotFoundHttpException) {
            $exception = new NotFoundException('The endpoint could not be found.');
        }

        if ($exception instanceof ThrottleRequestsException) {
            $exception = new TooManyRequestsException('Too many requests were made to this endpoint. Please wait before making any more requests.');
        }

        if ($exception instanceof MethodNotAllowedHttpException) {
            $exception = new MethodNotAllowedException($request->getMethod());
        }

        if ($exception instanceof MultiErrorInterface) {
            $errors = [];

            foreach ($exception->getErrors() as $error) {
                $errors[] = (new ErrorTransformer())->transform($error);
            }

            $errorResponse = array_filter([
                'errors' => $errors,
                'meta'   => $exception->getMeta(),
            ]);

            if ($this->debug === true) {
                $errorResponse['meta']['debug'] = $this->getDebugMeta($exception);
            }

            return $this->responseFactory->json(
                $errorResponse,
                $exception->getStatus(),
                [
                    'Content-Type' => 'application/vnd.api+json',
                ]
            );
        }

        if ($exception instanceof ExceptionInterface) {
            $error = (new ErrorTransformer())->transform($exception);

            if ($this->debug === true) {
                $error['meta']['debug'] = $this->getDebugMeta($exception);
            }

            return $this->responseFactory->json(
                [
                    'errors' => [
                        $error,
                    ],
                ],
                $exception->getStatus(),
                [
                    'Content-Type' => 'application/vnd.api+json',
                ]
            );
        }

        return $this->responseFactory->json(
            [
                'errors' => [
                    $this->getDefaultError($exception),
                ],
            ],
            Response::HTTP_INTERNAL_SERVER_ERROR,
            [
                'Content-Type' => 'application/vnd.api+json',
            ]
        );
    }

    /**
     * @param MaintenanceModeException $exception
     * @param Request                  $request
     * @return array
     */
    private function getMaintenanceJsonResponse(MaintenanceModeException $exception, $request)
    {
        if ($request->path() === '/') {
            return [
                'title'  => $this->appName,
                'status' => 'Service Unavailable',
            ];
        }

        $error = $this->getDefaultError($exception);
        $error['status'] = (string) Response::HTTP_SERVICE_UNAVAILABLE;

        return ['errors' => [$error]];
    }

    /**
     * Return the default error array/object.
     *
     * @param Exception $exception
     * @return array
     */
    private function getDefaultError(Exception $exception): array
    {
        $error = [
            'status' => (string) Response::HTTP_INTERNAL_SERVER_ERROR,
            'code'   => ExceptionInterface::INTERNAL_SERVER_ERROR['code'],
            'title'  => ExceptionInterface::INTERNAL_SERVER_ERROR['title'],
            'detail' => 'Something went wrong. Please try again. If the problem persists, contact support.',
        ];

        if (isset($this->contactLink)) {
            $error['links']['contact'] = $this->contactLink;
        }

        if ($this->debug === true) {
            $error['meta']['debug'] = $this->getDebugMeta($exception);
        }

        return $error;
    }

    /**
     * Report or log an exception.
     *
     * @param Exception $e
     */
    public function report(Exception $e): void
    {
        if ($this->shouldntReport($e)) {
            return;
        }

        if (!isset($this->logger)) {
            return;
        }

        $context = ['trace' => array_slice($e->getTrace(), 0, 5)];

        if ($e instanceof MultiErrorInterface) {
            $context['multi_error_errors'] = array_map(function (JsonSchemaErrorInterface $error) {
                return [
                    'code'   => $error->getErrorCode(),
                    'title'  => $error->getTitle(),
                    'detail' => $error->getDetail(),
                    'source' => $error->getSource(),
                ];
            }, $e->getErrors());
        }

        $this->isWarning($e)
            ? $this->logger->warning($e->getMessage(), $context)
            : $this->logger->error($e->getMessage(), $context);
    }

    private function isWarning(Exception $exception): bool
    {
        // Not all exceptions have the getStatus method.
        // We define it in the JsonSchemaErrorInterface, which all the exceptions that we throw implement.
        return $exception instanceof JsonSchemaErrorInterface
            && $exception->getStatus() !== null
            && $exception->getStatus() < 500;
    }

    /**
     * Get debug data from the exception to put in the meta of the response.
     *
     * @param Exception $exception
     * @return array
     */
    private function getDebugMeta(Exception $exception)
    {
        $trace = $exception->getTrace();

        try {
            if (json_encode($trace) === false) {
                $trace = 'Trace is not available.';
            }
        } catch (Throwable $e) {
            $trace = 'Trace is not available.';
        }

        return [
            'exception' => get_class($exception),
            'message'   => $exception->getMessage(),
            'trace'     => $trace,
        ];
    }
}
