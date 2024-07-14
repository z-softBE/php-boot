<?php

namespace PhpBoot\Utils;

final readonly class StringUtils
{
    private function __construct()
    {
    }

    public static function addTrailingString(string $value, string $trailingString): string
    {
        if (str_ends_with($value, $trailingString)) {
            return $value;
        }

        return $value . $trailingString;
    }

    public static function addBeginningString(string $value, string $beginningString): string
    {
        if (str_starts_with($value, $beginningString)) {
            return $value;
        }

        return $beginningString . $value;
    }

    public static function isBlank(string|null $value): bool
    {
        return $value === null || empty(trim($value));
    }
}