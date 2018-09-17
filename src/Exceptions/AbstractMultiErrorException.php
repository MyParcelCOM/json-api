<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Exceptions;

use Exception;
use MyParcelCom\JsonApi\Exceptions\Interfaces\JsonSchemaErrorInterface;
use MyParcelCom\JsonApi\Exceptions\Interfaces\MultiErrorInterface;

abstract class AbstractMultiErrorException extends Exception implements MultiErrorInterface
{
    /**
     * @var JsonSchemaErrorInterface[]
     */
    protected $errors;

    /**
     * @var array
     */
    protected $meta;

    /**
     * @var string
     */
    protected $status;

    /**
     * @return JsonSchemaErrorInterface[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param JsonSchemaErrorInterface[] $errors
     * @return $this
     */
    public function setErrors(array $errors): MultiErrorInterface
    {
        $this->errors = $errors;

        return $this;
    }

    /**
     * @return array
     */
    public function getMeta(): array
    {
        return $this->meta;
    }

    /**
     * @param array $meta
     * @return $this
     */
    public function setMeta(array $meta): MultiErrorInterface
    {
        $this->meta = $meta;

        return $this;
    }

    /**
     * Return the http status for the request.
     *
     * @return string
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * Set the http status code for the request.
     *
     * @param string $status
     * @return $this
     */
    public function setStatus(string $status): MultiErrorInterface
    {
        $this->status = $status;

        return $this;
    }
}
