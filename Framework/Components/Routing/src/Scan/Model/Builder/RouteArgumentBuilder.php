<?php

namespace PhpBoot\Http\Routing\Scan\Model\Builder;

use PhpBoot\Http\Routing\Attributes\Path\PathVariable;
use PhpBoot\Http\Routing\Attributes\Request\HeaderParam;
use PhpBoot\Http\Routing\Attributes\Request\QueryParam;
use PhpBoot\Http\Routing\Attributes\Request\RequestBody;
use PhpBoot\Http\Routing\Scan\Model\RouteArgument;
use PhpBoot\Http\Routing\Scan\Model\RouteArgumentType;
use ReflectionNamedType;

class RouteArgumentBuilder
{
    private RouteArgumentType $type;
    private PathVariable|RequestBody|HeaderParam|QueryParam|null $attribute = null;
    private string $parameterName;
    private ReflectionNamedType $reflectionType;
    private bool $isNullable = false;
    private mixed $defaultValue = null;

    public function withType(RouteArgumentType $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function withAttribute(PathVariable|RequestBody|HeaderParam|QueryParam|null $attribute): self
    {
        $this->attribute = $attribute;
        return $this;
    }

    public function withParameterName(string $parameterName): self
    {
        $this->parameterName = $parameterName;
        return $this;
    }

    public function withReflectionType(ReflectionNamedType $reflectionType): self
    {
        $this->reflectionType = $reflectionType;
        return $this;
    }

    public function withNullable(bool $nullable): self
    {
        $this->isNullable = $nullable;
        return $this;
    }

    public function withDefaultValue(mixed $defaultValue): self
    {
        $this->defaultValue = $defaultValue;
        return $this;
    }

    public function build(): RouteArgument
    {
        return new RouteArgument(
            $this->type,
            $this->attribute,
            $this->parameterName,
            $this->reflectionType,
            $this->isNullable,
            $this->defaultValue
        );
    }
}