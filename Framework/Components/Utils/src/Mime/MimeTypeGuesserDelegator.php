<?php

namespace PhpBoot\Utils\Mime;

class MimeTypeGuesserDelegator implements MimeTypeGuesser
{
    private static self|null $instance = null;

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /** @var MimeTypeGuesser[]  */
    private array $guessers;

    private function __construct()
    {
        $this->guessers = [];

        $this->registerGuesser(new MimeTypeGuesserFromConstant());
        $this->registerGuesser(new FileInfoMimeTypeGuesser());
    }

    public function isSupported(): bool
    {
        foreach ($this->guessers as $guesser) {
            if ($guesser->isSupported()) return true;
        }

        return false;
    }

    public function guessMimeType(string $path): string|null
    {
        foreach ($this->guessers as $guesser) {
            if (!$guesser->isSupported()) continue;

            $mimeType = $guesser->guessMimeType($path);

            if ($mimeType !== null) {
                return $mimeType;
            }
        }

        return null;
    }

    public function guessExtensionsByMimeType(string $mimeType): array
    {
        $mimeType = strtolower($mimeType);
        if (!isset(MimeTypeConstants::MIME_TYPE_TO_EXTENSION[$mimeType])) return [];

        return MimeTypeConstants::MIME_TYPE_TO_EXTENSION[$mimeType];
    }

    public function registerGuesser(MimeTypeGuesser $guesser): void
    {
        $this->guessers[] = $guesser;
    }
}