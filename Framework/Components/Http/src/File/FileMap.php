<?php

namespace PhpBoot\Http\File;

use PhpBoot\Utils\ArrayUtils;

class FileMap
{
    private const array FILE_KEYS = ['error', 'name', 'size', 'tmp_name', 'type'];

    /** @var array<string, UploadedFile|UploadedFile[]>  */
    private array $files;

    public function __construct(array $files)
    {
        $this->sanitizeFiles($files);
    }

    public function getFiles(): array
    {
        return $this->files;
    }

    public function hasFile(string $name): bool
    {
        return array_key_exists($name, $this->files);
    }

    /**
     * @param string $name
     * @param int|null $index
     * @return UploadedFile[]|UploadedFile|null
     */
    public function getFile(string $name, int|null $index): array|UploadedFile|null
    {
        if (!$this->hasFile($name)) return null;

        $value = $this->files[$name];

        if ($index !== null && is_array($value) && array_key_exists($index, $value)) {
            return $value[$index];
        }

        return $value;
    }

    private function sanitizeFiles(array $files): void
    {
        $this->files = [];
        foreach ($files as $fileKey => $file) {
            unset($file['full_path']);

            if (!ArrayUtils::doArraysHaveTheSameValues(self::FILE_KEYS, array_keys($file), true)) {
                continue;
            }

            if (is_array($file['name'])) {
                $this->files[$fileKey] = $this->sanitizeFileArray($file);
            } else {
                $this->files[$fileKey] = $this->sanitizeSingleFile($file);
            }
        }
    }

    private function sanitizeFileArray(array $arrayFile): array
    {
        $files = [];
        foreach ($arrayFile as $key => $values) {
            foreach ($values as $index => $value) {
                $files[$index][$key] = $value;
            }
        }

        return array_map(fn($arrayFile) => $this->sanitizeSingleFile($arrayFile), $files);
    }

    private function sanitizeSingleFile(array $file): UploadedFile
    {
        return new UploadedFile($file['tmp_name'], $file['name'], $file['type'], $file['error']);
    }
}