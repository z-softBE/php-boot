<?php

namespace PhpBoot\Scripts\MimeType;

use GuzzleHttp\Client;

class MimeDBScanner implements MimeTypeScanner
{

    private const string URL = 'https://cdn.jsdelivr.net/npm/mime-db@1.53.0/db.json';
    private const string EXTENSIONS = 'extensions';

    public function scan(MimeTypeInfoMap $map): void
    {
        $httpClient = new Client();
        $mimeDbData = json_decode(
            $httpClient->get(self::URL)->getBody()->getContents(), true
        );

        foreach ($mimeDbData as $mimeType => $mimeInfo) {
            if (!array_key_exists(self::EXTENSIONS, $mimeInfo)) continue;

            $map->addType($mimeType);
            foreach ($mimeInfo[self::EXTENSIONS] as $extension) {
                $map->addExtensionToType($extension, $mimeType);
            }
        }
    }
}