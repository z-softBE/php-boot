<?php

namespace PhpBoot\Di\Scan\Model;

use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;

readonly class ServiceInjectionInfo
{
    private ReflectionClass $class;
    private string|null $injectionName;
    private string $serviceAttributeClassName;
    private bool $primary;
    /** @var ConstructorInjectionArg[]  */
    private array $constructorArgs;
    private ReflectionMethod|null $beanMethod;
    private ReflectionNamedType|null $beanType;
    private string|null $configurationContainerKey;

    /**
     * @param ReflectionClass $class
     * @param string|null $injectionName
     * @param string $serviceAttributeClassName
     * @param bool $primary
     * @param ConstructorInjectionArg[] $constructorArgs
     * @param ReflectionMethod|null $beanMethod
     * @param ReflectionNamedType|null $beanType
     * @param string|null $configurationContainerKey
     */
    public function __construct(ReflectionClass          $class,
                                string|null              $injectionName,
                                string                   $serviceAttributeClassName,
                                bool                     $primary, array $constructorArgs,
                                ReflectionMethod|null    $beanMethod,
                                ReflectionNamedType|null $beanType,
                                string|null              $configurationContainerKey)
    {
        $this->class = $class;
        $this->injectionName = $injectionName;
        $this->serviceAttributeClassName = $serviceAttributeClassName;
        $this->primary = $primary;
        $this->constructorArgs = $constructorArgs;
        $this->beanMethod = $beanMethod;
        $this->beanType = $beanType;
        $this->configurationContainerKey = $configurationContainerKey;
    }

    public function getClass(): ReflectionClass
    {
        return $this->class;
    }

    public function getInjectionName(): string|null
    {
        return $this->injectionName;
    }

    public function getServiceAttributeClassName(): string
    {
        return $this->serviceAttributeClassName;
    }

    public function isPrimary(): bool
    {
        return $this->primary;
    }

    public function getConstructorArgs(): array
    {
        return $this->constructorArgs;
    }

    public function getBeanMethod(): ReflectionMethod|null
    {
        return $this->beanMethod;
    }

    public function getBeanType(): ReflectionNamedType|null
    {
        return $this->beanType;
    }

    public function getConfigurationContainerKey(): string|null
    {
        return $this->configurationContainerKey;
    }

    public function getContainerKey(): string
    {
        return $this->injectionName ?? $this->class->getName();
    }



}