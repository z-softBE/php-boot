<?php

namespace PhpBoot\Utils;

final readonly class FileUtils
{

    private function __construct()
    {
    }

    public static function getMaxFileSizeFromPHPSettings(): int
    {
        $postMaxSize = self::parseIniFileSize(ini_get('post_max_size'));
        $uploadMaxFileSize = self::parseIniFileSize(ini_get('upload_max_filesize'));

        return min(
          $postMaxSize ?? PHP_INT_MAX,
          $uploadMaxFileSize ?? PHP_INT_MAX
        );
    }

    private static function parseIniFileSize(string|false $size): int|null
    {
        if ($size === false || StringUtils::isBlank($size)) return null;

        $size = strtolower($size);

        $max = ltrim($size, '+');

        if (str_starts_with($max, '0x')) {
            $intSize = intval($max, 16);
        } else if (str_starts_with($max, '0')) {
            $intSize = intval($max, 8);
        } else {
            $intSize = (int) $max;
        }

        $unit = strtoupper(substr($size, -1));
        switch ($unit) {
            case 'T':
                $intSize *= 1024;
            case 'G':
                $intSize *= 1024;
            case 'M':
                $intSize *= 1024;
            case 'K':
                $intSize *= 1024;
        }

        return $intSize;
    }
}