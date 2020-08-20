<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Exceptions\Interfaces;

interface MultiErrorInterface
{
    /**
     * Set array of JSON API errors.
     *
     * @param JsonSchemaErrorInterface[] $errors
     * @return $this
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
     *
     * @return array
     */
    public function getMeta(): ?array;

    /**
     * Set meta object containing non-standard meta-information about the error.
     *
     * @param array $meta
     * @return $this
     */
    public function setMeta(array $meta): MultiErrorInterface;

    /**
     * Return the http status for the request.
     *
     * @return int
     */
    public function getStatus(): ?int;

    /**
     * Set the http status code for the request.
     *
     * @param int $status
     * @return $this
     */
    public function setStatus(int $status): self;
}
