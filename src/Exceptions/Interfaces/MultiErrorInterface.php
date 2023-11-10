<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Exceptions\Interfaces;

interface MultiErrorInterface
{
    /**
     * Set array of JSON API errors.
     *
     * @param JsonSchemaErrorInterface[] $errors
     */
    public function setErrors(array $errors): MultiErrorInterface;

    /**
     * Returns array of JSON API errors.
     *
     * @return JsonSchemaErrorInterface[]
     */
    public function getErrors(): array;

    /**
     * Get meta object containing non-standard meta-information about the error.
     */
    public function getMeta(): array;

    /**
     * Set meta object containing non-standard meta-information about the error.
     */
    public function setMeta(array $meta): MultiErrorInterface;

    /**
     * Return the http status for the request.
     */
    public function getStatus(): int;

    /**
     * Set the http status code for the request.
     */
    public function setStatus(int $status): self;
}
