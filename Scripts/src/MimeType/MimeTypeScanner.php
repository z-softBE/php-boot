<?php

namespace PhpBoot\Scripts\MimeType;

interface MimeTypeScanner
{
    public function scan(MimeTypeInfoMap $map): void;
}