<?php

namespace PhpBoot\Utils\Mime;

use PhpBoot\Utils\ArrayUtils;
use PhpBoot\Utils\StringUtils;

class MimeTypeGuesserFromConstant implements MimeTypeGuesser
{

    public function isSupported(): bool
    {
        return class_exists('PhpBoot\Utils\Mime\MimeTypeConstants');
    }

    public function guessMimeType(string $path): string|null
    {
        if (!$this->isSupported()) return null;

        $extension = pathinfo($path, PATHINFO_EXTENSION);

        if (StringUtils::isBlank($extension)) return null;

        return ArrayUtils::getFirstElement(MimeTypeConstants::EXTENSION_TO_MIME_TYPES[$extension]);
    }
}