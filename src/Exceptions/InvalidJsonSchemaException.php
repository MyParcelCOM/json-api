<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Exceptions;

use Symfony\Component\HttpFoundation\Response;

/**
 * This exception is thrown when a request contains invalid JSON Schema.
 */
class InvalidJsonSchemaException extends AbstractException
{
    /**
     * @param array           $errors
     * @param \Throwable|null $previous
     */
    public function __construct(array $errors, \Throwable $previous = null)
    {
        parent::__construct(
            'The supplied data is invalid according to our API Specification. See meta for details.',
            self::INVALID_JSON_SCHEMA,
            Response::HTTP_BAD_REQUEST,
            $previous
        );

        $this->setLinks([
            'specification' => 'https://docs.myparcel.com/api-specification',
        ]);
        $this->addMeta('json_schema_errors', $errors);
    }
}
