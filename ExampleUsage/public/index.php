<?php

use App\AppKernel;
use PhpBoot\Http\Request\RequestFactory;

error_reporting(E_ALL);
ini_set('display_errors', 1);

ini_set('xdebug.var_display_max_depth', 10);
ini_set('xdebug.var_display_max_children', 256);
ini_set('xdebug.var_display_max_data', 1024);

require dirname(__DIR__) . '/vendor/autoload.php';

$request = RequestFactory::createFromGlobals();
$kernel = new AppKernel();

$kernel->handleRequest($request);