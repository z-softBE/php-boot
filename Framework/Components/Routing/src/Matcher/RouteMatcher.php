<?php

namespace PhpBoot\Http\Routing\Matcher;

use PhpBoot\Http\Request\Request;
use PhpBoot\Http\Routing\Scan\Model\PathElementType;
use PhpBoot\Http\Routing\Scan\Model\RouteInfo;

class RouteMatcher
{
    /** @var RouteInfo[]  */
    private array $routes;

    /**
     * @param RouteInfo[] $routes
     */
    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    public function matchRoute(Request $request): RouteInfo|null
    {
        $uriParts = explode('/', $request->getRequestUri());
        foreach ($this->routes as $route) {
            if ($route->getHttpMethod() !== $request->getMethod()) continue;

            if (count($uriParts) !== count($route->getPath())) continue;

            $match = true;
            for ($i = 0; $i < count($uriParts); $i++) {
                $routePart = $route->getPath()[$i];
                $uriPart = $uriParts[$i];

                if ($routePart->getType() !== PathElementType::VARIABLE && $routePart->getValue() !== $uriPart) {
                    $match = false;
                    break;
                }
            }

            if ($match) {
                return $route;
            }
        }

        return null;
    }

}