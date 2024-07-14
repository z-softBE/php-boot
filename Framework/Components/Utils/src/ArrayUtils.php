<?php

namespace PhpBoot\Utils;

final readonly class ArrayUtils
{
    private function __construct()
    {
    }

    public static function getFirstElement(array $array): mixed
    {
        if (empty($array)) return null;

        return array_values($array)[0];
    }

    public static function doArraysHaveTheSameValues(array $array1, array $array2, bool $ignoreCurrentSort = true): bool
    {
        if ($ignoreCurrentSort) {
            sort($array1);
            sort($array2);
        }

        return $array1 == $array2;
    }

    public static function findIndex(array $array, callable $searchFn): string|int|null
    {
        foreach ($array as $key => $value) {
            if ($searchFn($value)) {
                return $key;
            }
        }

        return null;
    }
}