<?php declare(strict_types=1);

namespace MyParcelCom\Exceptions;

use Symfony\Component\HttpFoundation\Response;

/**
 * This exception is thrown when a token is invalid.
 */
class InvalidTokenException extends AbstractJsonApiException
{
    /**
     * @param string         $detail
     * @param Throwable|null $previous
     */
    public function __construct(string $detail, Throwable $previous = null)
    {
        parent::__construct(
            $detail,
            JsonApiExceptionInterface::OAUTH_INVALID_TOKEN,
            Response::HTTP_UNAUTHORIZED,
            $previous
        );
    }
}
