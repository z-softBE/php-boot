<?php

namespace PhpBoot\Scripts\MimeType;

use Nette\PhpGenerator\PhpNamespace;

final readonly class MimeTypeInfoCodeGenerator
{
    private function __construct()
    {
    }

    public static function generateMimeTypeInfoClass(MimeTypeInfoMap $mimeTypeInfoMap): void
    {
        $namespace = new PhpNamespace('PhpBoot\Utils\Mime');
        $class = $namespace->addClass('MimeTypeConstants')
            ->setReadOnly()
            ->setFinal();

        $class->addMethod('__construct')
            ->setPrivate();

        $class->addConstant('MIME_TYPE_TO_EXTENSION', self::getMimeTypeToExtensionArray($mimeTypeInfoMap))
            ->setPublic()
            ->setType('array');

        $class->addConstant('EXTENSION_TO_MIME_TYPES', self::getExtensionToMimeTypesArray($mimeTypeInfoMap))
            ->setPublic()
            ->setType('array');

        file_put_contents(
            dirname(__DIR__) . '/../../Framework/Components/Utils/src/Mime/MimeTypeConstants.php',
            "<?php\n\n". $namespace
        );
    }

    private static function getMimeTypeToExtensionArray(MimeTypeInfoMap $mimeTypeInfoMap): array
    {
        $types = [];
        foreach ($mimeTypeInfoMap->getMimeTypeInfos() as $mimeTypeInfo) {
            foreach ($mimeTypeInfo->getContentTypes() as $type) {
                $types[$type] = $mimeTypeInfo->getExtensions();
            }
        }
        return $types;
    }

    private static function getExtensionToMimeTypesArray(MimeTypeInfoMap $mimeTypeInfoMap): array
    {
        $extensions = [];
        foreach ($mimeTypeInfoMap->getMimeTypeInfos() as $mimeTypeInfo) {
            foreach ($mimeTypeInfo->getExtensions() as $extension) {
                $extensions[$extension] = $mimeTypeInfo->getContentTypes();
            }
        }
        return $extensions;
    }
}