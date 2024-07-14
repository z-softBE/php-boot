<?php

namespace PhpBoot\Utils\Mime;

use finfo;
use InvalidArgumentException;

class FileInfoMimeTypeGuesser implements MimeTypeGuesser
{

    public function isSupported(): bool
    {
        return function_exists('finfo_open');
    }

    public function guessMimeType(string $path): string|null
    {
        if (!$this->isSupported()) return null;

        if (!is_file($path) || !is_readable($path)) {
            throw new InvalidArgumentException("The '{$path}' file does not exists or is not readable");
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($path);

        if (!$mimeType) {
            return null;
        }

        return $mimeType;
    }
}