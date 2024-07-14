<?php

namespace PhpBoot\Di\Common;

use PhpBoot\Di\Exception\ReflectionTypeException;
use ReflectionIntersectionType;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionUnionType;

final readonly class ReflectionTypeValidator
{
    private function __construct()
    {
    }

    public static function validateConstructorParameterType(ReflectionParameter $parameter, string $serviceClass): void
    {
        $name = $parameter->getName();
        $type = $parameter->getType();

        if ($type === null) {
            throw new ReflectionTypeException("The constructor parameter '{$name}' in class '{$serviceClass}' 
            does not have a type. A type is required");
        }

        if ($type instanceof ReflectionUnionType || $type instanceof ReflectionIntersectionType) {
            throw new ReflectionTypeException("The constructor parameter '{$name}' in class '{$serviceClass}' 
            has multiple types. This is not (yet) supported.");
        }
    }

    public static function validateMethodReturnType(ReflectionMethod $method): void
    {
        $serviceClass = $method->getDeclaringClass()->getName();
        $methodName = $method->getName();
        $type = $method->getReturnType();

        if ($type === null) {
            throw new ReflectionTypeException("The return type of method '{$methodName}' in class '{$serviceClass}' 
            does not have a type. A return type is required!");
        }

        if ($type instanceof ReflectionUnionType || $type instanceof ReflectionIntersectionType) {
            throw new ReflectionTypeException("The return type of method '{$methodName}' in class '{$serviceClass}' 
            has multiple types. This is not (yet) supported.");
        }
    }

}