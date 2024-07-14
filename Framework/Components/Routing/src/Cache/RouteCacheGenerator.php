<?php

namespace PhpBoot\Http\Routing\Cache;

use Nette\PhpGenerator\PhpNamespace;
use PhpBoot\Di\Container\ServiceContainerInterface;
use PhpBoot\Http\Routing\Attributes\Path\PathVariable;
use PhpBoot\Http\Routing\Attributes\Request\HeaderParam;
use PhpBoot\Http\Routing\Attributes\Request\QueryParam;
use PhpBoot\Http\Routing\Attributes\Request\RequestBody;
use PhpBoot\Http\Routing\Attributes\Response\ResponseBody;
use PhpBoot\Http\Routing\Attributes\Response\ResponseStatus;
use PhpBoot\Http\Routing\Scan\Model\PathElement;
use PhpBoot\Http\Routing\Scan\Model\RouteArgument;
use PhpBoot\Http\Routing\Scan\Model\RouteInfo;
use PhpBoot\Utils\FileSystemUtils;

class RouteCacheGenerator
{

    /**
     * @param string $cacheDirectory
     * @param RouteInfo[] $routes
     * @return void
     */
    public function cacheRoutes(string $cacheDirectory, array $routes): void
    {
        $routeArray = array_map(fn(RouteInfo $route) => $this->createRouteInfo($route), $routes);

        $namespace = new PhpNamespace('Cache\\PhpBoot\\Http\\Routing');
        $class = $namespace->addClass('CachedRoutes');
        $class->setFinal()
            ->setReadOnly();

        $class->addConstant('ROUTES', $routeArray)
            ->setPrivate()
            ->setType('array');

        $method = $class->addMethod('getRoutes')
            ->setFinal()
            ->setPublic()
            ->setStatic();

        $method->addParameter('container')
            ->setType(ServiceContainerInterface::class);

        $method->addBody('$routs = [];')
            ->addBody('foreach (self::ROUTES as $route) {')
            ->addBody('$routes[] = \\PhpBoot\\Http\\Routing\\Scan\\Model\\RouteInfo::fromArray($route, $container);')
            ->addBody('}')
            ->addBody('return $routes;');

        $saveDirectory = $cacheDirectory . DIRECTORY_SEPARATOR . 'Http' . DIRECTORY_SEPARATOR . 'Routing';
        FileSystemUtils::createDirectory($saveDirectory);

        file_put_contents(
            $saveDirectory . DIRECTORY_SEPARATOR . 'CachedRoutes.php',
            "<?php\n\n" . $namespace
        );
    }

    private function createRouteInfo(RouteInfo $route): array
    {
        return [
            "routePathId" => $route->getRouteInfoId(),
            "controllerBeanName" => $route->getControllerBean()->getContainerKey(),
            "controllerClass" => $route->getMethod()->getDeclaringClass()->name,
            "httpMethod" => $route->getHttpMethod(),
            "path" => array_map(fn(PathElement $elem) => $this->createPathElement($elem), $route->getPath()),
            "arguments" => array_map(fn(RouteArgument $arg) => $this->createRouteArgument($arg), $route->getArguments()),
            "responseBody" => $this->createResponseBody($route->getResponseBody()),
            "responseStatus" => $this->createResponseStatus($route->getResponseStatus()),
            "methodName" => $route->getMethod()->name
        ];
    }

    private function createPathElement(PathElement $elem): array
    {
        return [
            "type" => $elem->getType(),
            "value" => $elem->getValue()
        ];
    }

    private function createRouteArgument(RouteArgument $arg): array
    {
        return [
            "type" => $arg->getType(),
            "attribute" => $this->createAttributeForRouteArgument($arg->getAttribute()),
            "parameterName" => $arg->getParameterName(),
            "nullable" => $arg->isNullable(),
            "defaultValue" => $arg->getDefaultValue()
        ];
    }

    private function createAttributeForRouteArgument(QueryParam|HeaderParam|PathVariable|RequestBody|null $attribute): array|null
    {
        if ($attribute === null) return null;

        return match (true) {
            $attribute instanceof QueryParam => ['type' => QueryParam::class, 'name' => $attribute->name, 'defaultValue' => $attribute->defaultValue, 'required' => $attribute->required],
            $attribute instanceof HeaderParam => ['type' => HeaderParam::class, 'name' => $attribute->name, 'defaultValue' => $attribute->defaultValue, 'required' => $attribute->required],
            $attribute instanceof RequestBody => ['type' => RequestBody::class, 'required' => $attribute->required],
            $attribute instanceof PathVariable => ['type' => PathVariable::class, 'name' => $attribute->name],
            default => null
        };
    }

    private function createResponseBody(ResponseBody|null $responseBody): array|null
    {
        if ($responseBody === null) return null;

        return [
          "type" => $responseBody->type,
          "produces" => $responseBody->produces
        ];
    }

    private function createResponseStatus(ResponseStatus|null $responseStatus): array|null
    {
        if ($responseStatus === null) return null;

        return [
            "statusCode" => $responseStatus->statusCode
        ];
    }
}