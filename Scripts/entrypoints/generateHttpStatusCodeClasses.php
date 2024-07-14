<?php

use PhpBoot\Scripts\HttpStatusCode\StatusCodeGenerator;
use PhpBoot\Scripts\HttpStatusCode\StatusJSScanner;

require dirname(__DIR__) . '/vendor/autoload.php';

$scanner = new StatusJSScanner();
$codes = $scanner->scan();

StatusCodeGenerator::generateHttpCodeClasses($codes);

echo "Generated HTTP status code classes\n";