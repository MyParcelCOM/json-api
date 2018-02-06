<?php declare(strict_types=1);

namespace MyParcelCom\Exceptions;

/**
 * This exception is a replacement for the OAuthServerException used by the League package.
 */
class AuthException extends AbstractJsonApiException
{
    /**
     * GenericOAuthException constructor.
     *
     * @param string          $detail
     * @param int             $status
     * @param \Throwable|null $previous
     */
    public function __construct(string $detail, int $status, \Throwable $previous = null)
    {
        parent::__construct(
            $detail,
            JsonApiExceptionInterface::AUTH_SERVER_EXCEPTION,
            $status,
            $previous
        );
    }
}
