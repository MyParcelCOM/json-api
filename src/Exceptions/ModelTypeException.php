<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Exceptions;

use RuntimeException;

class ModelTypeException extends RuntimeException
{
    /**
     * @param mixed  $model
     * @param string $expectedType
     */
    public function __construct($model, string $expectedType)
    {
        $type = is_object($model) ? get_class($model) : (string)$model;
        $message = 'Invalid model of type `' . $type . '`, expected model of type `' . $expectedType . '`';

        parent::__construct($message);
    }
}
