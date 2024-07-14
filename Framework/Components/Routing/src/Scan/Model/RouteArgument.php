<?php

namespace PhpBoot\Http\Routing\Scan\Model;

use PhpBoot\Http\Routing\Attributes\Path\PathVariable;
use PhpBoot\Http\Routing\Attributes\Request\HeaderParam;
use PhpBoot\Http\Routing\Attributes\Request\QueryParam;
use PhpBoot\Http\Routing\Attributes\Request\RequestBody;
use ReflectionNamedType;

readonly class RouteArgument
{
    private RouteArgumentType $type;
    private PathVariable|RequestBody|HeaderParam|QueryParam|null $attribute;
    private string $parameterName;
    private ReflectionNamedType $reflectionType;
    private bool $isNullable;
    private mixed $defaultValue;

    /**
     * @param RouteArgumentType $type
     * @param PathVariable|HeaderParam|QueryParam|RequestBody|null $attribute
     * @param string $parameterName
     * @param ReflectionNamedType $reflectionType
     * @param bool $isNullable
     * @param mixed $defaultValue
     */
    public function __construct(
        RouteArgumentType                                    $type,
        QueryParam|HeaderParam|PathVariable|RequestBody|null $attribute,
        string                                               $parameterName,
        ReflectionNamedType                                  $reflectionType,
        bool                                                 $isNullable,
        mixed                                                $defaultValue)
    {
        $this->type = $type;
        $this->attribute = $attribute;
        $this->parameterName = $parameterName;
        $this->reflectionType = $reflectionType;
        $this->isNullable = $isNullable;
        $this->defaultValue = $defaultValue;
    }

    public function getType(): RouteArgumentType
    {
        return $this->type;
    }

    public function getAttribute(): QueryParam|HeaderParam|PathVariable|RequestBody|null
    {
        return $this->attribute;
    }

    public function getParameterName(): string
    {
        return $this->parameterName;
    }

    public function getReflectionType(): ReflectionNamedType
    {
        return $this->reflectionType;
    }

    public function isNullable(): bool
    {
        return $this->isNullable;
    }

    public function getDefaultValue(): mixed
    {
        return $this->defaultValue;
    }



}