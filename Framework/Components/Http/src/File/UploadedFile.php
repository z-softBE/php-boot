<?php

namespace PhpBoot\Http\File;

use PhpBoot\Http\Exception\FileException;
use PhpBoot\Utils\ArrayUtils;
use PhpBoot\Utils\Mime\MimeTypeGuesserDelegator;

class UploadedFile extends File
{
    private const array UPLOAD_ERROR_MESSAGE = [
        UPLOAD_ERR_INI_SIZE => 'The file "%s" exceeds your upload_max_filesize init directive (limit is %d Kib)',
        UPLOAD_ERR_FORM_SIZE => 'The file "%s" exceeds the upload limit defined in your form.',
        UPLOAD_ERR_PARTIAL => 'The file "%s" was only partially uploaded',
        UPLOAD_ERR_NO_FILE => 'No file was uploaded',
        UPLOAD_ERR_CANT_WRITE => 'The file "%s" could not be written on disk',
        UPLOAD_ERR_NO_TMP_DIR => 'File could not be uploaded: missing temp directory',
        UPLOAD_ERR_EXTENSION => 'File upload was stopped by a PHP extension'
    ];

    private readonly string $originalName;
    private readonly string $mimeType;
    private readonly int $uploadError;

    /**
     * @param string $path
     * @param string $originalName
     * @param string $mimeType
     * @param int $uploadError
     */
    public function __construct(string $path, string $originalName, string $mimeType, int $uploadError)
    {
        $this->originalName = $originalName;
        $this->mimeType = $mimeType;
        $this->uploadError = $uploadError;

        parent::__construct($path);
    }

    public function getOriginalName(): string
    {
        return $this->originalName;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function getUploadError(): int
    {
        return $this->uploadError;
    }

    public function isValid(): bool
    {
        return $this->uploadError === UPLOAD_ERR_OK && is_uploaded_file($this->getPathname());
    }

    public function getOriginalExtension(): string
    {
        return pathinfo($this->originalName, PATHINFO_EXTENSION);
    }

    public function guessExtensionFromMimeType(): string|null
    {
        $extensions = MimeTypeGuesserDelegator::getInstance()->guessExtensionsByMimeType($this->mimeType);
        return ArrayUtils::getFirstElement($extensions);
    }

    public function move(string $directory, string|null $name): File
    {
        if (!$this->isValid()) {
            throw new FileException($this->getUploadedErrorMessage());
        }

        $targetFile = $this->getTargetFile($directory, $name);
        $renamed = rename($this->getPathname(), $targetFile->getPathname());

        if (!$renamed) {
            $errorMsg = strip_tags(error_get_last());
            throw new FileException("Could not move file '{$this->getPathname()}' to '{$targetFile->getPathname()}' ({$errorMsg})");
        }

        @chmod($targetFile->getPathname(), 0666 & ~umask());

        return $targetFile;
    }
}