<?php declare(strict_types=1);

namespace MyParcelCom\Exceptions;

use Symfony\Component\HttpFoundation\Response;

/**
 * This exception is thrown when a scope is either not available at all,
 * or unavailable for the chosen grant type.
 *
 * @package App\Exceptions
 */
class InvalidScopeException extends AbstractJsonApiException
{
    /**
     * InvalidScopeException constructor.
     *
     * @param string         $detail
     * @param Throwable|null $previous
     */
    public function __construct(string $detail, Throwable $previous = null)
    {
        parent::__construct(
            $detail,
            JsonApiExceptionInterface::OAUTH_INVALID_SCOPE,
            Response::HTTP_FORBIDDEN,
            $previous
        );
    }
}
