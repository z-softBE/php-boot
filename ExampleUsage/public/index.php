<?php

use PhpBoot\Di\Property\PropertiesReader;
use PhpBoot\Di\Property\PropertyRegistry;
use PhpBoot\Di\Scan\ServiceScanner;

error_reporting(E_ALL);
ini_set('display_errors', 1);

ini_set('xdebug.var_display_max_depth', 10);
ini_set('xdebug.var_display_max_children', 256);
ini_set('xdebug.var_display_max_data', 1024);

require dirname(__DIR__) . '/vendor/autoload.php';

$propertyReader = new PropertiesReader();
$readProperties = $propertyReader->readProperties(dirname(__DIR__) . '/config/properties.yaml');
$propertyRegistry = new PropertyRegistry($readProperties);

var_dump($propertyRegistry);
var_dump($propertyRegistry->getPropertyByName('hello.world'));
var_dump($propertyRegistry->getPropertyByNameOrDefault('hello.world2', 'default'));
die;

$psr4Mappings = require dirname(__DIR__) . '/vendor/composer/autoload_psr4.php';
$namespacesToScan = ['App'];

$serviceScanner = new ServiceScanner($psr4Mappings, $namespacesToScan);
$scannedServices = $serviceScanner->scan();

var_dump($scannedServices);