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
}
