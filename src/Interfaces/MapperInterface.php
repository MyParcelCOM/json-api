<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Interfaces;

interface MapperInterface
{
    /**
     * Maps given data to given model and returns the model.
     *
     * @param mixed  $data
     * @param object $model
     * @return object
     */
    public function map($data, $model);
}
