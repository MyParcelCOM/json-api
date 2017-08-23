<?php

namespace MyParcelCom\Exceptions;

use Exception;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Foundation\Http\Exceptions\MaintenanceModeException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Intouch\Newrelic\Newrelic;

class Handler extends ExceptionHandler
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

    /** @var Newrelic */
    protected $newrelic;

    /**
     * Set the Response Factory.
     *
     * @param  ResponseFactory $factory
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
     * @param  bool $debug
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
     * @param  string $link
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
     * @param  LoggerInterface $logger
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
     * @param  Request   $request
     * @param  Exception $exception
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
            $exception = new NotFoundException(
                "The endpoint could not be found."
            );
        }

        if ($exception instanceof JsonApiExceptionInterface) {
            $error = (new JsonApiErrorTransformer())->transform($exception);

            if ($this->debug === true) {
                $error['meta'] = array_merge($error['meta'] ?? [], $this->getDebugMeta($exception));
            }

            return $this->responseFactory->json([
                'errors' => [
                    $error,
                ],
            ], $exception->getStatus());
        }

        return $this->responseFactory->json(
            [
                "errors" => [
                    $this->getDefaultError($exception),
                ],
            ],
            Response::HTTP_INTERNAL_SERVER_ERROR
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
        $error['status'] = (string)Response::HTTP_SERVICE_UNAVAILABLE;

        return ['errors' => [$error]];
    }

    /**
     * Return the default error array/object.
     *
     * @param  Exception $exception
     * @return array
     */
    private function getDefaultError(Exception $exception): array
    {
        $error = [
            "status" => (string)Response::HTTP_INTERNAL_SERVER_ERROR,
            "code"   => JsonApiExceptionInterface::INTERNAL_SERVER_ERROR["code"],
            "title"  => JsonApiExceptionInterface::INTERNAL_SERVER_ERROR["title"],
            "detail" => "Something went wrong. Please try again. If the problem persists, contact support.",
        ];

        if (isset($this->contactLink)) {
            $error['links']['contact'] = $this->contactLink;
        }

        if ($this->debug === true) {
            $error['meta'] = $this->getDebugMeta($exception);
        }

        return $error;
    }

    /**
     * Report or log an exception.
     *
     * @param  Exception $e
     * @return void
     */
    public function report(Exception $e): void
    {
        if (isset($this->newrelic)) {
            $this->newrelic->noticeError($e->getMessage(), $e);
        }

        if (!isset($this->logger)) {
            return;
        }

        $this->logger->error($e->getMessage(), $e->getTrace());
    }

    /**
     * Set Newrelic
     *
     * @param Newrelic $newrelic
     */
    public function setNewrelic(Newrelic $newrelic)
    {
        $this->newrelic = $newrelic;
    }

    /**
     * Get debug data from the exception to put in the meta of the response.
     *
     * @param Exception $exception
     * @return array
     */
    private function getDebugMeta(Exception $exception)
    {
        return [
            'exception' => get_class($exception),
            'message'   => $exception->getMessage(),
            'trace'     => $exception->getTrace(),
        ];
    }
}
