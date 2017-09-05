<?php declare(strict_types=1);

namespace MyParcelCom\Common\Contracts;

interface MapperInterface
{
    public function map($data, $model);
}
