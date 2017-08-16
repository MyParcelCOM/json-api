<?php

namespace MyParcelCom\Exceptions;

use Exception;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * @var ResponseFactory
     */
    protected $responseFactory;

    /**
     * @var bool
     */
    protected $debug;

    /**
     * @var string
     */
    protected $contactLink;

    /**
     * @var LoggerInterface
     */
    protected $logger;

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
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception               $exception
     * @return \Illuminate\Http\JsonResponse
     */
    public function render($request, Exception $exception)
    {
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
                    $error
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
        if (!isset($this->logger)) {
            return;
        }

        $this->logger->error($e->getMessage(), $e->getTrace());
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
