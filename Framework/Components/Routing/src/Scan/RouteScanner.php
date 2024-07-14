<?php

namespace PhpBoot\Http\Routing\Scan;

use PhpBoot\Di\Common\ReflectionTypeValidator;
use PhpBoot\Di\Container\BeanInfo;
use PhpBoot\Di\Container\ServiceContainerInterface;
use PhpBoot\Di\Exception\ReflectionTypeException;
use PhpBoot\Http\Request\Request;
use PhpBoot\Http\Routing\Attributes\Controller;
use PhpBoot\Http\Routing\Attributes\Path\Path;
use PhpBoot\Http\Routing\Attributes\Path\PathVariable;
use PhpBoot\Http\Routing\Attributes\Request\HeaderParam;
use PhpBoot\Http\Routing\Attributes\Request\QueryParam;
use PhpBoot\Http\Routing\Attributes\Request\RequestBody;
use PhpBoot\Http\Routing\Attributes\Response\ResponseBody;
use PhpBoot\Http\Routing\Attributes\Response\ResponseStatus;
use PhpBoot\Http\Routing\Exception\PathInvalidException;
use PhpBoot\Http\Routing\Scan\Model\Builder\PathElementBuilder;
use PhpBoot\Http\Routing\Scan\Model\Builder\RouteArgumentBuilder;
use PhpBoot\Http\Routing\Scan\Model\Builder\RouteInfoBuilder;
use PhpBoot\Http\Routing\Scan\Model\PathElement;
use PhpBoot\Http\Routing\Scan\Model\PathElementType;
use PhpBoot\Http\Routing\Scan\Model\RouteArgument;
use PhpBoot\Http\Routing\Scan\Model\RouteArgumentType;
use PhpBoot\Http\Routing\Scan\Model\RouteInfo;
use PhpBoot\Utils\ArrayUtils;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;

class RouteScanner
{
    /**
     * @param ServiceContainerInterface $container
     * @return RouteInfo[]
     * @throws PathInvalidException
     * @throws ReflectionTypeException
     */
    public function scanForRoutes(ServiceContainerInterface $container): array
    {
        $controllers = $container->getAllBeansByAttributeType(Controller::class);

        $routes = [];

        foreach ($controllers as $controller) {
            $routes = array_merge($routes, $this->scanForRoutesInController($controller));
        }

        return $routes;
    }

    /**
     * @param BeanInfo $controller
     * @return RouteInfo[]
     * @throws PathInvalidException
     * @throws ReflectionTypeException
     * @throws ReflectionException
     */
    private function scanForRoutesInController(BeanInfo $controller): array
    {
        $routes = [];
        $class = new ReflectionClass($controller->getService());

        foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            $pathAttributes = $method->getAttributes(Path::class, ReflectionAttribute::IS_INSTANCEOF);

            if (empty($pathAttributes)) continue;

            if (count($pathAttributes) > 1) {
                throw new PathInvalidException("The method '{$method->getName()}' from controller '{$class->getName()}' 
                has more than 1 Path attribute");
            }

            ReflectionTypeValidator::validateMethodReturnType($method);

            /** @var Path $pathAttribute */
            $pathAttribute = ArrayUtils::getFirstElement($pathAttributes)->newInstance();

            $builder = new RouteInfoBuilder();
            $route = $builder
                ->withControllerBean($controller)
                ->withHttpMethod($pathAttribute->method)
                ->withPath($this->stripPath($pathAttribute->path))
                ->withArguments($this->getRouteArguments($method->getParameters(), $method->getName(), $class->getName()))
                ->withResponseBody($this->getFirstAttributeOrNull($method->getAttributes(ResponseBody::class, ReflectionAttribute::IS_INSTANCEOF)))
                ->withResponseStatus($this->getFirstAttributeOrNull($method->getAttributes(ResponseStatus::class, ReflectionAttribute::IS_INSTANCEOF)))
                ->withReturnType($method->getReturnType())
                ->withMethod($method)
                ->build();

            ScannedRouteValidator::validateRoute($route, $method);

            $routes[] = $route;
        }

        return $routes;
    }

    /**
     * @param string $path
     * @return PathElement[]
     */
    private function stripPath(string $path): array
    {
        $pathElements = [];
        foreach (explode('/', $path) as $part) {
            $builder = new PathElementBuilder();

            if (str_starts_with($part, '{') && str_ends_with($part, '}')) {
                $builder->withType(PathElementType::VARIABLE)
                    ->withValue(ltrim(rtrim($part, '}'), '{'));
            } else {
                $builder->withType(PathElementType::NORMAL)
                    ->withValue($part);
            }

            $pathElements[] = $builder->build();
        }

        return $pathElements;
    }

    /**
     * @param ReflectionParameter[] $parameters
     * @param string $methodName
     * @param string $className
     * @return RouteArgument[]
     * @throws ReflectionTypeException
     * @throws PathInvalidException
     */
    private function getRouteArguments(array $parameters, string $methodName, string $className): array
    {
        if (empty($parameters)) return [];

        $routeArguments = [];
        foreach ($parameters as $parameter) {
            ReflectionTypeValidator::validateMethodParameterType($parameter, $className, $methodName);

            /** @var ReflectionNamedType $reflectionType */
            $reflectionType = $parameter->getType();

            $builder = new RouteArgumentBuilder();
            $builder
                ->withParameterName($parameter->getName())
                ->withReflectionType($reflectionType)
                ->withNullable($parameter->allowsNull() || $reflectionType->allowsNull());

            if ($parameter->isDefaultValueAvailable()) {
                $builder->withDefaultValue($parameter->getDefaultValue());
            }

            if ($reflectionType->getName() === Request::class || is_subclass_of($reflectionType->getName(), Request::class)) {
                $builder
                    ->withType(RouteArgumentType::REQUEST)
                    ->withAttribute(null);
            } else {
                $attributes = $parameter->getAttributes();

                if (count($attributes) != 1) {
                    throw new PathInvalidException("The parameter '{$parameter->getName()}' 
                    from method '{$methodName}' from controller '{$className}' 
                    should have exactly 1 attribute or be of the Request type");
                }

                /** @var ReflectionAttribute $attribute */
                $attribute = ArrayUtils::getFirstElement($attributes);
                $type = match ($attribute->getName()) {
                    PathVariable::class => RouteArgumentType::PATH_VARIABLE,
                    QueryParam::class => RouteArgumentType::QUERY_PARAM,
                    HeaderParam::class => RouteArgumentType::HEADER_PARAM,
                    RequestBody::class => RouteArgumentType::REQUEST_BODY,
                    default => throw new PathInvalidException("The parameter '{$parameter->getName()}' 
                    from method '{$methodName}' from controller '{$className}' 
                    has an attribute that we do not support.")
                };

                $attributeInstance = $attribute->newInstance();
                if (
                    ($type === RouteArgumentType::QUERY_PARAM || $type === RouteArgumentType::HEADER_PARAM) &&
                    $attributeInstance->defaultValue !== null
                ) {
                    $builder->withDefaultValue($attributeInstance->defaultValue);
                }

                $builder
                    ->withType($type)
                    ->withAttribute($attributeInstance);
            }

            $routeArguments[] = $builder->build();
        }

        return $routeArguments;
    }

    private function getFirstAttributeOrNull(array $attributes): mixed
    {
        if (count($attributes) === 0) return null;

        return ArrayUtils::getFirstElement($attributes)->newInstance();
    }
}