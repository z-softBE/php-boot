<?php

namespace PhpBoot\Http\Routing\Scan;

use PhpBoot\Http\Response\Response;
use PhpBoot\Http\Routing\Attributes\Response\ResponseBodyType;
use PhpBoot\Http\Routing\Exception\PathInvalidException;
use PhpBoot\Http\Routing\Scan\Model\RouteArgument;
use PhpBoot\Http\Routing\Scan\Model\RouteArgumentType;
use PhpBoot\Http\Routing\Scan\Model\RouteInfo;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

final readonly class ScannedRouteValidator
{
    private const array QUERY_PARAMS_ALLOWED_TYPES = ['array', 'bool', 'int', 'float', 'string'];
    private const array HEADER_PARAMS_ALLOWED_TYPES = ['bool', 'int', 'float', 'string'];

    private function __construct()
    {
    }

    /**
     * @param RouteInfo $route
     * @param ReflectionMethod $method
     * @return void
     * @throws PathInvalidException
     * @throws ReflectionException
     */
    public static function validateRoute(RouteInfo $route, ReflectionMethod $method): void
    {
        self::validateReturnType($route, $method);

        foreach ($route->getArguments() as $argument) {
            self::validateRequestBody($argument, $method);
            self::validateQueryParam($argument, $method);
            self::validateHeaderParam($argument, $method);
        }
    }

    /**
     * @param RouteInfo $route
     * @param ReflectionMethod $method
     * @return void
     * @throws PathInvalidException
     * @throws ReflectionException
     */
    private static function validateReturnType(RouteInfo $route, ReflectionMethod $method): void
    {
        if ($route->getReturnType()->isBuiltin()) return;

        $returnTypeClass = new ReflectionClass($route->getReturnType()->getName());
        if (
            $returnTypeClass->getName() !== Response::class &&
            !$returnTypeClass->isSubclassOf(Response::class) &&
            $route->getResponseBody() === null
        ) {
            throw new PathInvalidException(
                "The method '{$method->getName()}' from controller '{$method->getDeclaringClass()->getName()}' 
                does not return a Response object and does not have a ResponseBody attribute"
            );
        }

        if (
            $route->getResponseBody() !== null &&
            $route->getResponseBody()->type ===ResponseBodyType::RAW &&
            !$returnTypeClass->hasMethod('__toString')
        ) {
            throw new PathInvalidException(
                "The method '{$method->getName()}' from controller '{$method->getDeclaringClass()->getName()}' 
                has a ResponseBody attribute with the type 'RAW'. But the returned object does not have a __toString method"
            );
        }
    }

    /**
     * @param RouteArgument $argument
     * @param ReflectionMethod $method
     * @return void
     * @throws PathInvalidException
     */
    private static function validateRequestBody(RouteArgument $argument, ReflectionMethod $method): void
    {
        if ($argument->getType() !== RouteArgumentType::REQUEST_BODY) return;

        self::validateOptionalParameter('RequestBody', $argument, $method);
    }

    /**
     * @param RouteArgument $argument
     * @param ReflectionMethod $method
     * @return void
     * @throws PathInvalidException
     */
    private static function validateQueryParam(RouteArgument $argument, ReflectionMethod $method): void
    {
        if ($argument->getType() !== RouteArgumentType::QUERY_PARAM) return;

        self::validateOptionalParameter('QueryParam', $argument, $method);
        self::validateParameterType('QueryParam', self::QUERY_PARAMS_ALLOWED_TYPES, $argument, $method);
    }

    /**
     * @param RouteArgument $argument
     * @param ReflectionMethod $method
     * @return void
     * @throws PathInvalidException
     */
    private static function validateHeaderParam(RouteArgument $argument, ReflectionMethod $method): void
    {
        if ($argument->getType() !== RouteArgumentType::HEADER_PARAM) return;

        self::validateOptionalParameter('HeaderParam', $argument, $method);
        self::validateParameterType('HeaderParam', self::HEADER_PARAMS_ALLOWED_TYPES, $argument, $method);
    }

    /**
     * @param string $type
     * @param RouteArgument $argument
     * @param ReflectionMethod $method
     * @return void
     * @throws PathInvalidException
     */
    private static function validateOptionalParameter(string $type, RouteArgument $argument, ReflectionMethod $method): void
    {
        if (
            !$argument->getAttribute()->required &&
            !$argument->isNullable() &&
            $argument->getDefaultValue() === null
        ) {
            throw new PathInvalidException(
              "The {$type} '\${$argument->getParameterName()}' is optional 
              but there is no default value configured and it not nullable in 
              method '{$method->getName()}' of class '{$method->getDeclaringClass()->getName()}'"
            );
        }
    }

    /**
     * @param string $type
     * @param string[] $allowedTypes
     * @param RouteArgument $argument
     * @param ReflectionMethod $method
     * @return void
     * @throws PathInvalidException
     */
    private static function validateParameterType(
        string $type, array $allowedTypes, RouteArgument $argument, ReflectionMethod $method): void
    {
        if (!in_array($argument->getReflectionType()->getName(), $allowedTypes)) {
            throw new PathInvalidException(
                "The {$type} '\${$argument->getParameterName()}' 
                has the type '{$argument->getReflectionType()->getName()}' 
                in method '{$method->getName()}' of class '{$method->getDeclaringClass()->getName()}'.
                This type is not supported"
            );
        }
    }
}