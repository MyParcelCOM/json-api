<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Exceptions;

use Exception;
use MyParcelCom\JsonApi\Exceptions\Interfaces\JsonSchemaErrorInterface;
use MyParcelCom\JsonApi\Exceptions\Interfaces\MultiErrorInterface;
use Throwable;

abstract class AbstractMultiErrorException extends Exception implements MultiErrorInterface
{
    protected array $meta = [];

    public function __construct(
        protected array $errors,
        protected int $status,
        Throwable $previous = null
    ) {
        parent::__construct('Response contains multiple errors.', $status, $previous);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function setErrors(array $errors): MultiErrorInterface
    {
        $this->errors = $errors;

        return $this;
    }

    public function getMeta(): array
    {
        return $this->meta;
    }

    public function setMeta(array $meta): MultiErrorInterface
    {
        $this->meta = $meta;

        return $this;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): MultiErrorInterface
    {
        $this->status = $status;

        return $this;
    }
}
