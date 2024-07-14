<?php

namespace PhpBoot\Di\Container;

use ReflectionClass;

readonly class BeanInfo
{
    private string $attributeType;
    private string|null $injectionName;
    private bool $isPrimary;
    /** @var string[]  */
    private array $possibleInjectionNames;
    private string $containerKey;

    private object $service;

    /**
     * @param string $attributeType
     * @param string|null $injectionName
     * @param bool $isPrimary
     * @param string $containerKey
     * @param object $service
     */
    public function __construct(string      $attributeType,
                                string|null $injectionName,
                                bool        $isPrimary,
                                string      $containerKey,
                                object      $service)
    {
        $this->attributeType = $attributeType;
        $this->injectionName = $injectionName;
        $this->isPrimary = $isPrimary;
        $this->containerKey = $containerKey;
        $this->service = $service;
        $this->possibleInjectionNames = $this->initPossibleInjectionNames();
    }

    public function getAttributeType(): string
    {
        return $this->attributeType;
    }

    public function getInjectionName(): string|null
    {
        return $this->injectionName;
    }

    public function isPrimary(): bool
    {
        return $this->isPrimary;
    }

    public function getPossibleInjectionNames(): array
    {
        return $this->possibleInjectionNames;
    }

    public function getContainerKey(): string
    {
        return $this->containerKey;
    }

    public function getService(): object
    {
        return $this->service;
    }

    /**
     * @return string[]
     */
    private function initPossibleInjectionNames(): array
    {
        $class = new ReflectionClass($this->service);
        return array_merge(
          [$class->getName()],
          $this->getAllParentClassNames($class),
          $class->getInterfaceNames()
        );
    }

    /**
     * @param ReflectionClass $class
     * @param string[] $classes
     * @return string[]
     */
    private function getAllParentClassNames(ReflectionClass $class, array $classes = []): array
    {
        $parent = $class->getParentClass();

        if ($parent !== false) {
            $classes = array_merge($classes, $this->getAllParentClassNames($parent, $classes));
        }

        return $classes;
    }


}