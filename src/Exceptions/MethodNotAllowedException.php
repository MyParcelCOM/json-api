<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * This exception is thrown when an endpoint does not support the http method used to call the endpoint.
 *
 * For example:
 * A `PUT` request is made to `/some-endpoint`, but `/some-endpoint` only supports GET requests.
 */
class MethodNotAllowedException extends AbstractException
{
    public function __construct(string $httpMethod, Throwable $previous = null)
    {
        $httpMethod = strtoupper($httpMethod);
        parent::__construct(
            "The '{$httpMethod}' method is not allowed on this endpoint.",
            self::METHOD_NOT_ALLOWED,
            Response::HTTP_METHOD_NOT_ALLOWED,
            $previous
        );
    }
}
