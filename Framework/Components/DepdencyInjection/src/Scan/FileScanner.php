<?php

namespace PhpBoot\Di\Scan;

use DirectoryIterator;
use ReflectionClass;
use ReflectionException;

final readonly class FileScanner
{
    private function __construct()
    {

    }

    /**
     * @param string $namespace
     * @param string $directory
     * @param bool $recursive
     * @return ReflectionClass[]
     */
    public static function scanForClassesInNamespace(string $namespace, string $directory, bool $recursive): array
    {
        $namespace = rtrim($namespace, '\\');

        $classes = [];
        foreach (new DirectoryIterator($directory) as $fileInfo) {
            if ($fileInfo->isDot()) continue;

            if ($recursive && $fileInfo->isDir()) {
                $baseName = $fileInfo->getBasename();
                $subNamespace = $namespace . '\\' . $baseName;
                $classes = array_merge($classes, self::scanForClassesInNamespace($subNamespace, $fileInfo->getRealPath(), true));
            } else if ($fileInfo->isFile()) {
                $baseName = $fileInfo->getBasename('.php');
                $className = $namespace . '\\' . $baseName;

                try {
                    $classes[] = new ReflectionClass($className);
                } catch (ReflectionException $ex) {
                    // Nothing we can do, skip it
                }
            }
        }
        return $classes;
    }


}