<?php

namespace PhpBoot\Di\Scan\Model;

use PhpBoot\Di\Attribute\Property;
use PhpBoot\Di\Exception\ServiceScannerException;
use PhpBoot\Di\Scan\Model\Builder\ConstructorInjectionArgBuilder;
use PhpBoot\Utils\ArrayUtils;
use ReflectionClass;
use ReflectionParameter;

readonly class ConstructorInjectionArg
{
    public static function formArray(array $config, ReflectionClass $reflectionClass): self
    {
        $builder = new ConstructorInjectionArgBuilder();
        return $builder
            ->withParameter(self::findConstructorParameterByName($reflectionClass, $config['parameterName']))
            ->withType($config['type'])
            ->withQualifier($config['qualifier'])
            ->build();
    }

    private static function findConstructorParameterByName(ReflectionClass $class, string $paramName): ReflectionParameter
    {
        foreach ($class->getConstructor()->getParameters() as $parameter) {
            if ($parameter->name === $paramName) {
                return $parameter;
            }
        }

        throw new ServiceScannerException("Could not find the constructor param of class '{$class->name}' with name '{$paramName}'");
    }

    private ReflectionParameter $parameter;
    private ConstructorInjectionArgType $type;
    private string|null $qualifier;

    /**
     * @param ReflectionParameter $parameter
     * @param ConstructorInjectionArgType $type
     * @param string|null $qualifier
     */
    public function __construct(ReflectionParameter         $parameter,
                                ConstructorInjectionArgType $type,
                                string|null                 $qualifier)
    {
        $this->parameter = $parameter;
        $this->type = $type;
        $this->qualifier = $qualifier;
    }

    public function getParameter(): ReflectionParameter
    {
        return $this->parameter;
    }

    public function getType(): ConstructorInjectionArgType
    {
        return $this->type;
    }

    public function getQualifier(): string|null
    {
        return $this->qualifier;
    }

    public function hasQualifier(): bool
    {
        return $this->qualifier !== null;
    }

    public function hasDefaultValue(): bool
    {
        return $this->parameter->isDefaultValueAvailable();
    }

    public function allowsNull(): bool
    {
        return $this->parameter->allowsNull() || $this->parameter->getType()->allowsNull();
    }

    public function getPropertyName(): string|null
    {
        if ($this->type !== ConstructorInjectionArgType::PROPERTY) return null;

        $propertyAttributes = $this->parameter->getAttributes(Property::class, \ReflectionAttribute::IS_INSTANCEOF);

        if (empty($propertyAttributes)) return null;

        return ArrayUtils::getFirstElement($propertyAttributes)->newInstance()->name;
    }

    public function getParameterClassName(): string
    {
        return $this->parameter->getType()->getName();
    }

    public function getInjectionName(): string
    {
        return $this->qualifier ?? $this->getParameterClassName();
    }

}