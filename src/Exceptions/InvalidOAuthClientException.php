<?php declare(strict_types=1);

namespace MyParcelCom\Exceptions;

use Symfony\Component\HttpFoundation\Response;

/**
 * This exception is throws when the client cannot be validated by its id and secret.
 *
 * @package App\Exceptions
 */
class InvalidOAuthClientException extends AbstractJsonApiException
{
    /**
     * InvalidOAuthClientException constructor.
     *
     * @param string         $detail
     * @param array          $errorType
     * @param Throwable|null $previous
     */
    public function __construct(string $detail, array $errorType, Throwable $previous = null)
    {
        parent::__construct($detail, $errorType, Response::HTTP_FORBIDDEN, $previous);
    }
}
