<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * This exception is thrown when a request contains invalid JSON Schema.
 */
class InvalidJsonSchemaException extends AbstractException
{
    public function __construct(array $errors, Throwable $previous = null)
    {
        parent::__construct(
            'The supplied data is invalid according to our API Specification. See meta for details.',
            self::INVALID_JSON_SCHEMA,
            Response::HTTP_BAD_REQUEST,
            $previous,
        );

        $this->setLinks([
            'specification' => 'https://api-specification.myparcel.com',
        ]);
        $this->addMeta('json_schema_errors', $errors);
    }
}
