<?php

use PhpBoot\Scripts\MimeType\FreeDesktopScanner;
use PhpBoot\Scripts\MimeType\MimeDBScanner;
use PhpBoot\Scripts\MimeType\MimeTypeInfoCodeGenerator;
use PhpBoot\Scripts\MimeType\MimeTypeInfoMap;
use PhpBoot\Scripts\MimeType\MimeTypeScanner;

require dirname(__DIR__) . '/vendor/autoload.php';

$mimeTypeInfoMap = new MimeTypeInfoMap();
$scanners = [
  new MimeDBScanner(),
  new FreeDesktopScanner()
];

/** @var MimeTypeScanner $scanner */
foreach ($scanners as $scanner) {
    $scanner->scan($mimeTypeInfoMap);
}

MimeTypeInfoCodeGenerator::generateMimeTypeInfoClass($mimeTypeInfoMap);
echo "MimeTypeConstants.php generated!\n";