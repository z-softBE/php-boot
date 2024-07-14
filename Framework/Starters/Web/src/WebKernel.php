<?php

namespace PhpBoot\Starter\Web;

use Doctrine\DBAL\Exception;
use JMS\Serializer\Serializer;
use PhpBoot\Core\Kernel;
use PhpBoot\Event\EventSystem;
use PhpBoot\Event\Model\Event;
use PhpBoot\Exception\ExceptionHandlerDelegator;
use PhpBoot\Http\Request\Request;
use PhpBoot\Http\Routing\Generator\RouteArgumentGenerator;
use PhpBoot\Http\Routing\Generator\RouteResponseGenerator;
use PhpBoot\Http\Routing\Matcher\RouteMatcher;
use PhpBoot\Http\Routing\Router;
use PhpBoot\Http\Routing\Scan\RouteScanner;
use PhpBoot\Starter\Web\Interceptor\InterceptorChain;

abstract class WebKernel extends Kernel
{
    protected Router $router;
    protected InterceptorChain $interceptorChain;
    protected EventSystem $eventSystem;
    protected ExceptionHandlerDelegator $exceptionHandlerDelegator;

    protected array $cachedRouteInfo;

    public function __construct()
    {
        parent::__construct();

        $this->interceptorChain = $this->serviceContainer->get('phpBoot.interceptorChain');
        $this->eventSystem = $this->serviceContainer->get('phpBoot.eventSystem');
        $this->exceptionHandlerDelegator = $this->serviceContainer->get('phpBoot.exceptionHandlerDelegator');

        $this->createRouter();
    }

    public function handleRequest(Request $request): void
    {
        try {
            $this->eventSystem->sendEvent(new Event('handleRequest.start', ['request' => $request]));
            $this->interceptorChain->preRequest($request);

            $response = $this->router->createResponseFromRequest($request);
            $response->prepare($request);

            $this->interceptorChain->postRequest($request, $response);
            $this->eventSystem->sendEvent(new Event('handleRequest.sendResponse', ['request' => $request, 'response' => $response]));
        } catch (\Exception $exception) {
            $response = $this->exceptionHandlerDelegator->handleException($exception);
        }

        $response->send();
    }

    protected function createRouter(): void
    {
        $this->generateCachedRouteInfo();

        $serializer = $this->serviceContainer->getFirstBeanByClass(Serializer::class);

        $this->router = new Router(
            new RouteMatcher($this->cachedRouteInfo),
            new RouteArgumentGenerator($serializer),
            new RouteResponseGenerator($serializer),
            $this->eventSystem
        );
    }

    protected function generateCachedRouteInfo(): void
    {
        $routeScanner = new RouteScanner();
        $this->cachedRouteInfo = $routeScanner->scanForRoutes($this->serviceContainer);
    }
}