<?php

namespace PhpBoot\Starter\Web;

use JMS\Serializer\Serializer;
use PhpBoot\Core\Kernel;
use PhpBoot\Http\Request\Request;
use PhpBoot\Http\Routing\Generator\RouteArgumentGenerator;
use PhpBoot\Http\Routing\Generator\RouteResponseGenerator;
use PhpBoot\Http\Routing\Matcher\RouteMatcher;
use PhpBoot\Http\Routing\Router;
use PhpBoot\Http\Routing\Scan\RouteScanner;

abstract class WebKernel extends Kernel
{
    protected Router $router;

    protected array $cachedRouteInfo;

    public function __construct()
    {
        parent::__construct();

        $this->createRouter();
    }

    public function handleRequest(Request $request): void
    {
        $response = $this->router->createResponseFromRequest($request);
        $response->prepare($request);

        $response->send();
    }

    protected function createRouter(): void
    {
        $this->generateCachedRouteInfo();

        $serializer = $this->serviceContainer->getFirstBeanByClass(Serializer::class);

        $this->router = new Router(
            new RouteMatcher($this->cachedRouteInfo),
            new RouteArgumentGenerator($serializer),
            new RouteResponseGenerator($serializer)
        );
    }

    protected function generateCachedRouteInfo(): void
    {
        $routeScanner = new RouteScanner();
        $this->cachedRouteInfo = $routeScanner->scanForRoutes($this->serviceContainer);
    }
}