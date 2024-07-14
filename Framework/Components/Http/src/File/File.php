<?php

namespace PhpBoot\Http\File;

use PhpBoot\Http\Exception\FileException;
use PhpBoot\Utils\FileSystemUtils;
use PhpBoot\Utils\StringUtils;
use SplFileInfo;

class File extends SplFileInfo
{
    public function getContent(): string
    {
        $content = file_get_contents($this->getPathname());

        if ($content === false) {
            throw new FileException("Could not get the content of file: {$this->getPathname()}");
        }

        return $content;
    }

    public function getTargetFile(string $directory, string|null $name = null): self
    {
        FileSystemUtils::createDirectory($directory);

        if (!is_writable($directory)) {
            throw new FileException("Unable to write into directory: {$directory}");
        }

        $filename = StringUtils::isBlank($name) ? $this->getBasename() : $name;
        $targetPath = $directory . DIRECTORY_SEPARATOR . $filename;

        return new self($targetPath);
    }
}