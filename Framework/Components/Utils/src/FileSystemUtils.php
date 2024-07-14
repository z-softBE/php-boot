<?php

namespace PhpBoot\Utils;

final readonly class FileSystemUtils
{
    private function __construct()
    {
    }

    public static function createDirectory(string $path, int $permissions = 0777): void
    {
       if (is_dir($path)) return;

       mkdir($path, $permissions, true);
    }

    public static function fileExists(string $filePath): bool
    {
        return file_exists($filePath);
    }
}