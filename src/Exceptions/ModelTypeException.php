<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Exceptions;

use RuntimeException;

class ModelTypeException extends RuntimeException
{
    public function __construct(mixed $model, string $expectedType)
    {
        $type = is_object($model) ? get_class($model) : (string) $model;
        $message = 'Invalid model of type `' . $type . '`, expected model of type `' . $expectedType . '`';

        parent::__construct($message);
    }
}
