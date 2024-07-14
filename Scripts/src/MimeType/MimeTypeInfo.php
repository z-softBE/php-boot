<?php

namespace PhpBoot\Scripts\MimeType;

class MimeTypeInfo
{
    /** @var string[]  */
    private array $extensions;

    /** @var string[]  */
    private array $contentTypes;

    /**
     * @param string[] $extensions
     * @param string[] $contentTypes
     */
    public function __construct(array $extensions = [], array $contentTypes = [])
    {
        $this->extensions = $extensions;
        $this->contentTypes = $contentTypes;
    }

    public function getExtensions(): array
    {
        return $this->extensions;
    }

    public function getContentTypes(): array
    {
        return $this->contentTypes;
    }

    public function hasExtension(string $extension): bool
    {
        return in_array(strtolower($extension), $this->extensions);
    }

    public function hasContentType(string $contentType): bool
    {
        return in_array(strtolower($contentType), $this->contentTypes);
    }

    public function addExtension(string $extension): void
    {
        if ($this->hasExtension($extension)) return;

        $this->extensions[] = $extension;
    }

    public function addContentType(string $contentType): void
    {
        if ($this->hasContentType($contentType)) return;

        $this->contentTypes[] = $contentType;
    }


}