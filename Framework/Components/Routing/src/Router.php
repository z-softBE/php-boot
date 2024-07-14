<?php

namespace PhpBoot\Http\Routing;

use PhpBoot\Http\Request\Request;
use PhpBoot\Http\Response\Response;
use PhpBoot\Http\Routing\Exception\RouteNotFoundException;
use PhpBoot\Http\Routing\Generator\RouteArgumentGenerator;
use PhpBoot\Http\Routing\Generator\RouteResponseGenerator;
use PhpBoot\Http\Routing\Matcher\RouteMatcher;

readonly class Router
{
    private RouteMatcher $routeMatcher;
    private RouteArgumentGenerator $routeArgumentGenerator;
    private RouteResponseGenerator $routeResponseGenerator;

    /**
     * @param RouteMatcher $routeMatcher
     * @param RouteArgumentGenerator $routeArgumentGenerator
     * @param RouteResponseGenerator $routeResponseGenerator
     */
    public function __construct(
        RouteMatcher $routeMatcher,
        RouteArgumentGenerator $routeArgumentGenerator,
        RouteResponseGenerator $routeResponseGenerator)
    {
        $this->routeMatcher = $routeMatcher;
        $this->routeArgumentGenerator = $routeArgumentGenerator;
        $this->routeResponseGenerator = $routeResponseGenerator;
    }

    public function createResponseFromRequest(Request $request): Response
    {
        $route = $this->routeMatcher->matchRoute($request);

        if ($route === null) {
            throw new RouteNotFoundException('Route not found!');
        }

        $controller = $route->getControllerBean()->getService();
        $executionArguments = $this->routeArgumentGenerator->generateRouteArguments($route, $request);

        $result = $route->getMethod()->invoke($controller, $executionArguments);

        if ($result instanceof Response) {
            return $result;
        }

        return $this->routeResponseGenerator->generateResponse($route->getResponseBody(), $route->getResponseStatus(), $result);
    }

}