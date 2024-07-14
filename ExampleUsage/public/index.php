<?php

use PhpBoot\Di\Inject\ServiceCreator;
use PhpBoot\Di\Property\PropertiesReader;
use PhpBoot\Di\Property\PropertyRegistry;
use PhpBoot\Di\Scan\ServiceScanner;
use PhpBoot\Http\Common\HttpStatusCode;
use PhpBoot\Http\Request\RequestFactory;
use PhpBoot\Http\Response\JsonResponse;
use PhpBoot\Http\Response\Response;

error_reporting(E_ALL);
ini_set('display_errors', 1);

ini_set('xdebug.var_display_max_depth', 10);
ini_set('xdebug.var_display_max_children', 256);
ini_set('xdebug.var_display_max_data', 1024);

require dirname(__DIR__) . '/vendor/autoload.php';

$request = RequestFactory::createFromGlobals();
$response = new JsonResponse(
    ['hello' => 'world'],
    HttpStatusCode::HTTP_CREATED,
    ['x-custom-header' => 'abc-123']
);
$response->prepare($request);
$response->send();
die;

$propertyReader = new PropertiesReader();
$readProperties = $propertyReader->readProperties(dirname(__DIR__) . '/config/properties.yaml');
$propertyRegistry = new PropertyRegistry($readProperties);

$psr4Mappings = require dirname(__DIR__) . '/vendor/composer/autoload_psr4.php';
$namespacesToScan = ['App'];

$serviceScanner = new ServiceScanner($psr4Mappings, $namespacesToScan);
$scannedServices = $serviceScanner->scan();

$serviceCreator = new ServiceCreator($propertyRegistry);
$beanMap = $serviceCreator->createServices($scannedServices);

var_dump($beanMap);