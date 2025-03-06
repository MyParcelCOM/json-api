<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Traits;

use BackedEnum;
use UnitEnum;

trait EnumTrait
{
    public function getEnumValue(mixed $enum): mixed
    {
        return match (true) {
            $enum instanceof BackedEnum => $enum->value,
            $enum instanceof UnitEnum   => $enum->name,
            default                     => $enum,
        };
    }
}
