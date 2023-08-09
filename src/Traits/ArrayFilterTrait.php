<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Traits;

trait ArrayFilterTrait
{
    /**
     * Do a deep filter on an array to remove:
     * - null values
     * - empty strings
     * - leftover empty arrays
     */
    private function arrayDeepFilter(array $array): array
    {
        $array = array_filter($array, fn ($var) => $var !== null && $var !== '');

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = $this->arrayDeepFilter($value);

                if (count($array[$key]) < 1) {
                    unset($array[$key]);
                }
            }
        }

        return $array;
    }
}
