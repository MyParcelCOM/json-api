<?php declare(strict_types=1);

namespace MyParcelCom\Exceptions;

use Symfony\Component\HttpFoundation\Response;

/**
 * This exception is throws when the client cannot be validated by its id and secret.
 */
class InvalidClientException extends AbstractJsonApiException
{
    /**
     * InvalidOAuthClientException constructor.
     */
    public function __construct()
    {
        parent::__construct(
            "The supplied client credentials are invalid or the client does not have access to this grant type.",
            JsonApiExceptionInterface::AUTH_INVALID_CLIENT,
            Response::HTTP_FORBIDDEN
        );
    }
}
