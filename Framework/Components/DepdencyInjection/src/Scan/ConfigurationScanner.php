<?php

namespace PhpBoot\Di\Scan;

use PhpBoot\Di\Attribute\Bean;
use PhpBoot\Di\Attribute\Primary;
use PhpBoot\Di\Common\ReflectionTypeValidator;
use PhpBoot\Di\Exception\ReflectionTypeException;
use PhpBoot\Di\Scan\Model\Builder\ServiceInjectionInfoBuilder;
use PhpBoot\Di\Scan\Model\ServiceInjectionInfo;
use PhpBoot\Utils\ArrayUtils;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;

class ConfigurationScanner
{

    /**
     * @param ReflectionClass $class
     * @param string $configurationContainerKey
     * @return ServiceInjectionInfo[]
     * @throws ReflectionTypeException
     */
    public function scanConfiguration(ReflectionClass $class, string $configurationContainerKey): array
    {
        $beans = [];
        foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            $beanAttribute = $this->getBeanAttribute($method);

            if ($beanAttribute === null) continue;

            ReflectionTypeValidator::validateMethodReturnType($method);

            $builder = new ServiceInjectionInfoBuilder();
            $beans[] = $builder
                ->withClass($method->getDeclaringClass())
                ->withInjectionName($beanAttribute->name ?? $method->getName())
                ->withServiceAttributeClassName(Bean::class)
                ->withPrimary($this->hasPrimaryAttribute($method))
                ->withBeanMethod($method)
                ->withBeanType($method->getReturnType())
                ->withConfigurationContainerKey($configurationContainerKey)
                ->build();
        }
        return $beans;
    }

    private function getBeanAttribute(ReflectionMethod $method): Bean|null
    {
        $attributes = $method->getAttributes(Bean::class, ReflectionAttribute::IS_INSTANCEOF);

        if (empty($attributes)) return null;

        return ArrayUtils::getFirstElement($attributes)->newInstance();
    }

    private function hasPrimaryAttribute(ReflectionMethod $method): bool
    {
        $primaryAttributes = $method->getAttributes(Primary::class, ReflectionAttribute::IS_INSTANCEOF);
        return count($primaryAttributes) > 0;
    }
}