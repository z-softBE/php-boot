<?php

namespace PhpBoot\Utils\Mime;

interface MimeTypeGuesser
{

    public function isSupported(): bool;

    public function guessMimeType(string $path): string|null;
}