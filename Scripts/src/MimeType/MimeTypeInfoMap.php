<?php

namespace PhpBoot\Scripts\MimeType;

class MimeTypeInfoMap
{
    /** @var MimeTypeInfo[]  */
    private array $mimeTypeInfos = [];

    public function addType(string $type): void
    {
        if ($this->getByType($type) !== null) return;

        $this->mimeTypeInfos[] = new MimeTypeInfo([], [$type]);
    }

    public function addAliasType(string $alias, string $type): void
    {
        $existing = $this->getByType($type);
        if ($existing === null) return;

        $existing->addContentType($alias);
    }

    public function addExtensionToType(string $extension, string $type): void
    {
        $existing = $this->getByType($type);
        if ($existing === null) {
            $this->mimeTypeInfos[] = new MimeTypeInfo([$extension], [$type]);
        } else {
            $existing->addExtension($extension);
        }
    }

    public function getByType(string $type): MimeTypeInfo|null
    {
        foreach ($this->mimeTypeInfos as $mimeTypeInfo) {
            if ($mimeTypeInfo->hasContentType($type)) {
                return $mimeTypeInfo;
            }
        }

        return null;
    }

    public function getMimeTypeInfos(): array
    {
        return $this->mimeTypeInfos;
    }

}