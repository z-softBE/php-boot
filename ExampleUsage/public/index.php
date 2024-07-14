<?php

use JMS\Serializer\SerializerBuilder;
use PhpBoot\Di\Container\ServiceContainer;
use PhpBoot\Di\Inject\ServiceCreator;
use PhpBoot\Di\Property\PropertiesReader;
use PhpBoot\Di\Property\PropertyRegistry;
use PhpBoot\Di\Scan\ServiceScanner;
use PhpBoot\Http\Common\HttpStatusCode;
use PhpBoot\Http\Request\RequestFactory;
use PhpBoot\Http\Response\JsonResponse;
use PhpBoot\Http\Response\Response;
use PhpBoot\Http\Routing\Generator\RouteArgumentGenerator;
use PhpBoot\Http\Routing\Generator\RouteResponseGenerator;
use PhpBoot\Http\Routing\Matcher\RouteMatcher;
use PhpBoot\Http\Routing\Router;
use PhpBoot\Http\Routing\Scan\RouteScanner;

error_reporting(E_ALL);
ini_set('display_errors', 1);

ini_set('xdebug.var_display_max_depth', 10);
ini_set('xdebug.var_display_max_children', 256);
ini_set('xdebug.var_display_max_data', 1024);

require dirname(__DIR__) . '/vendor/autoload.php';

$propertyReader = new PropertiesReader();
$readProperties = $propertyReader->readProperties(dirname(__DIR__) . '/config/properties.yaml');
$propertyRegistry = new PropertyRegistry($readProperties);

$psr4Mappings = require dirname(__DIR__) . '/vendor/composer/autoload_psr4.php';
$namespacesToScan = ['App'];

$serviceScanner = new ServiceScanner($psr4Mappings, $namespacesToScan);
$scannedServices = $serviceScanner->scan();

$serviceCreator = new ServiceCreator($propertyRegistry);
$beanMap = $serviceCreator->createServices($scannedServices);

$container = new ServiceContainer($beanMap);

$routeScanner = new RouteScanner();
$routes = $routeScanner->scanForRoutes($container);

$serializer = SerializerBuilder::create()->build();
$router = new Router(
    new RouteMatcher($routes),
    new RouteArgumentGenerator($serializer),
    new RouteResponseGenerator($serializer)
);

$request = RequestFactory::createFromGlobals();
$response = $router->createResponseFromRequest($request);

$response->prepare($request);
$response->send();