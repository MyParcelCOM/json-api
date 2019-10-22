<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Exceptions;

use Exception;
use MyParcelCom\JsonApi\Exceptions\Interfaces\JsonSchemaErrorInterface;
use MyParcelCom\JsonApi\Exceptions\Interfaces\MultiErrorInterface;
use Throwable;

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
     * @var int
     */
    protected $status;

    /**
     * @param JsonSchemaErrorInterface[] $errors
     * @param int                        $status
     * @param Throwable|null             $previous
     */
    public function __construct(array $errors, int $status, \Throwable $previous = null)
    {
        $this->setStatus($status);
        $this->setErrors($errors);

        parent::__construct('Response contains multiple errors.', $status, $previous);
    }

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
     * @return array|null
     */
    public function getMeta(): ?array
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
     * @return int
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * Set the http status code for the request.
     *
     * @param int $status
     * @return $this
     */
    public function setStatus(int $status): MultiErrorInterface
    {
        $this->status = $status;

        return $this;
    }
}
