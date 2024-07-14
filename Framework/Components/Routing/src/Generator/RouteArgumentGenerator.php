<?php

namespace PhpBoot\Http\Routing\Generator;

use JMS\Serializer\Serializer;
use PhpBoot\Http\Request\Request;
use PhpBoot\Http\Routing\Attributes\Path\PathVariable;
use PhpBoot\Http\Routing\Attributes\Request\HeaderParam;
use PhpBoot\Http\Routing\Attributes\Request\QueryParam;
use PhpBoot\Http\Routing\Attributes\Request\RequestBody;
use PhpBoot\Http\Routing\Exception\PathInvalidException;
use PhpBoot\Http\Routing\Scan\Model\PathElement;
use PhpBoot\Http\Routing\Scan\Model\PathElementType;
use PhpBoot\Http\Routing\Scan\Model\RouteArgument;
use PhpBoot\Http\Routing\Scan\Model\RouteArgumentType;
use PhpBoot\Http\Routing\Scan\Model\RouteInfo;
use PhpBoot\Utils\ArrayUtils;
use PhpBoot\Utils\StringUtils;
use ReflectionMethod;

readonly class RouteArgumentGenerator
{
    private Serializer $serializer;

    /**
     * @param Serializer $serializer
     */
    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param RouteInfo $route
     * @param Request $request
     * @return array
     * @throws PathInvalidException
     */
    public function generateRouteArguments(RouteInfo $route, Request $request): array
    {
        $executionArguments = [];

        foreach ($route->getArguments() as $argument) {
            $executionArguments[] = match ($argument->getType()) {
                RouteArgumentType::PATH_VARIABLE => $this->getPathVariable($route, $argument, $request),
                RouteArgumentType::HEADER_PARAM => $this->getHeaderParam($route, $argument, $request),
                RouteArgumentType::QUERY_PARAM => $this->getQueryParam($route, $argument, $request),
                RouteArgumentType::REQUEST_BODY => $this->createRequestBody($route, $argument, $request),
                RouteArgumentType::REQUEST => $request
            };
        }

        return $executionArguments;
    }

    /**
     * @param RouteInfo $route
     * @param RouteArgument $argument
     * @param Request $request
     * @return string
     */
    private function getPathVariable(RouteInfo $route, RouteArgument $argument, Request $request): string
    {
        /** @var PathVariable $attribute */
        $attribute = $argument->getAttribute();
        $nameToFind = $attribute->name ?? $argument->getParameterName();

        $index = ArrayUtils::findIndex(
            $route->getPath(),
            fn(PathElement $element) => $element->getType() === PathElementType::VARIABLE && $element->getValue() === $nameToFind
        );

        return explode('/', $request->getRequestUri())[$index];
    }

    /**
     * @param RouteInfo $route
     * @param RouteArgument $argument
     * @param Request $request
     * @return string|null
     * @throws PathInvalidException
     */
    private function getHeaderParam(RouteInfo $route, RouteArgument $argument, Request $request): string|null
    {
        /** @var HeaderParam $attribute */
        $attribute = $argument->getAttribute();
        $name = $attribute->name ?? $argument->getParameterName();
        $value = $request->getHeaders()->get($name);

        return $this->getValueOrDefaultForParam($value, $argument, $route->getMethod());
    }

    /**
     * @param RouteInfo $route
     * @param RouteArgument $argument
     * @param Request $request
     * @return string|null
     * @throws PathInvalidException
     */
    private function getQueryParam(RouteInfo $route, RouteArgument $argument, Request $request): string|null
    {
        /** @var QueryParam $attribute */
        $attribute = $argument->getAttribute();
        $name = $attribute->name ?? $argument->getParameterName();
        $value = $request->getQuery()->get($name);

        return $this->getValueOrDefaultForParam($value, $argument, $route->getMethod());
    }

    private function createRequestBody(RouteInfo $route, RouteArgument $argument, Request $request): mixed
    {
        /** @var RequestBody $attribute */
        $attribute = $argument->getAttribute();

        if ($request->hasXmlBody()) {
            return $this->serializer->deserialize($request->getRawContent(), $argument->getReflectionType()->getName(), 'xml');
        } else if ($request->hasJsonBody()) {
            return $this->serializer->deserialize($request->getRawContent(), $argument->getReflectionType()->getName(), 'json');
        } else if ($request->hasFormUrlEncodedBody() || $request->hasFormDataBody()) {
            $json = json_encode($request->getPost()->getAll());
            return $this->serializer->deserialize($json, $argument->getReflectionType()->getName(), 'json');
        }

        if (!$attribute->required) {
            return null;
        }

        throw new PathInvalidException(
            "The RequestBody '\${$argument->getParameterName()}' is required 
            but we could not find a body or deserialize it 
            in method '{$route->getMethod()->getName()}' of class '{$route->getMethod()->getDeclaringClass()->getName()}'.
            Be sure to add a body and Content-Type header"
        );
    }

    /**
     * @param mixed $value
     * @param RouteArgument $argument
     * @param ReflectionMethod $method
     * @return string|float|int|bool|array|null
     * @throws PathInvalidException
     */
    private function getValueOrDefaultForParam(
        mixed $value, RouteArgument $argument, ReflectionMethod $method): string|float|int|bool|array|null
    {
        /** @var HeaderParam|QueryParam $attribute */
        $attribute = $argument->getAttribute();
        $hasValue = !StringUtils::isBlank($value);
        $paramType = match (true) {
            $attribute instanceof HeaderParam => 'HeaderParam',
            $attribute instanceof QueryParam => 'QueryParam',
        };

        if (!$hasValue && $attribute->required) {
            throw new PathInvalidException("The {$paramType} '\${$argument->getParameterName()}' is required 
            in method '{$method->getName()}' of class '{$method->getDeclaringClass()->getName()}'");
        }

        if ($hasValue) {
            return match ($argument->getReflectionType()->getName()) {
                "int" => (int)$value,
                "float" => (float)$value,
                "bool" => filter_var($value, FILTER_VALIDATE_BOOL),
                default => $value
            };
        }

        if ($argument->getDefaultValue() !== null) {
            return $argument->getDefaultValue();
        }

        if ($argument->isNullable()) {
            return null;
        }

        throw new PathInvalidException(
            "The {$paramType} '\${$argument->getParameterName()}' is optional
            but there is no default value configured or not nullable  
            in method '{$method->getName()}' of class '{$method->getDeclaringClass()->getName()}'"
        );
    }

}