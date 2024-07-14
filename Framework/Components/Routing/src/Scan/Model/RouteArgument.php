<?php

namespace PhpBoot\Http\Routing\Scan\Model;

use PhpBoot\Http\Routing\Attributes\Path\PathVariable;
use PhpBoot\Http\Routing\Attributes\Request\HeaderParam;
use PhpBoot\Http\Routing\Attributes\Request\QueryParam;
use PhpBoot\Http\Routing\Attributes\Request\RequestBody;
use PhpBoot\Http\Routing\Exception\PathInvalidException;
use PhpBoot\Http\Routing\Scan\Model\Builder\RouteArgumentBuilder;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;

readonly class RouteArgument
{
    public static function fromArray(array $config, ReflectionMethod $method): self
    {
        $foundParameter = self::getMethodParameterFromArray($method, $config['parameterName']);

        $builder = new RouteArgumentBuilder();
        return $builder
            ->withType($config['type'])
            ->withAttribute(self::getAttributeFromArray($config['attribute']))
            ->withParameterName($config['parameterName'])
            ->withReflectionType($foundParameter->getType())
            ->withNullable($config['nullable'])
            ->withDefaultValue($config['defaultValue'])
            ->build();
    }

    private static function getMethodParameterFromArray(ReflectionMethod $method, string $paramName): ReflectionParameter
    {
        foreach ($method->getParameters() as $parameter) {
            if ($parameter->name === $paramName) {
                return $parameter;
            }
        }

        throw new PathInvalidException("Could not find the parameter with name '{$paramName}' 
        in method '{$method->name}' in class '{$method->getDeclaringClass()->name}'");
    }

    private static function getAttributeFromArray(array|null $attributeConfig): QueryParam|HeaderParam|PathVariable|RequestBody|null
    {
        if ($attributeConfig === null) return null;

        return match ($attributeConfig['type']) {
            QueryParam::class => new QueryParam($attributeConfig['name'], $attributeConfig['defaultValue'], $attributeConfig['required'] ?? false),
            HeaderParam::class => new HeaderParam($attributeConfig['name'], $attributeConfig['defaultValue'], $attributeConfig['required'] ?? false),
            RequestBody::class => new RequestBody($attributeConfig['required'] ?? false),
            PathVariable::class => new PathVariable($attributeConfig['name']),
        };
    }

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