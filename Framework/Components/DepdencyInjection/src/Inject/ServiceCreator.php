<?php

namespace PhpBoot\Di\Inject;

use PhpBoot\Di\Container\BeanInfo;
use PhpBoot\Di\Exception\BeanCreationException;
use PhpBoot\Di\Exception\CircularDependencyException;
use PhpBoot\Di\Property\PropertyRegistry;
use PhpBoot\Di\Scan\Model\ConstructorInjectionArg;
use PhpBoot\Di\Scan\Model\ConstructorInjectionArgType;
use PhpBoot\Di\Scan\Model\ServiceInjectionInfo;
use PhpBoot\Utils\Structure\Map;
use ReflectionNamedType;

readonly class ServiceCreator
{
    private const int MAX_LOOP_COUNT = 255;

    private PropertyRegistry $propertyRegistry;

    /**
     * @param PropertyRegistry $propertyRegistry
     */
    public function __construct(PropertyRegistry $propertyRegistry)
    {
        $this->propertyRegistry = $propertyRegistry;
    }

    /**
     * @param ServiceInjectionInfo[] $scannedServices
     * @return Map<string,BeanInfo>
     */
    public function createServices(array $scannedServices): Map
    {
        $beanMap = new Map();

        $this->createBeanMap($scannedServices, $beanMap);

        return $beanMap;
    }

    /**
     * @param ServiceInjectionInfo[] $scannedServices
     * @param Map $beanMap
     * @param array $cachedDependencies
     * @param int $loopCount
     * @return void
     */
    private function createBeanMap(
        array $scannedServices, Map &$beanMap,
        array $cachedDependencies = [], int $loopCount = 0
    ): void
    {
        foreach ($scannedServices as $key => $scannedService) {
            $dependencies = $this->getDependencies($scannedService, $cachedDependencies, $scannedServices);

            if ($this->areAllDependenciesResolved($beanMap, $dependencies)) {
                $beanName = $scannedService->getContainerKey();

                if ($beanMap->has($beanName)) {
                    throw new BeanCreationException("There is already a bean with the name '{$beanName}'. 
                    All beans need to have unique names.");
                }

                $createBean = BeanFactory::createBean($scannedService, $beanMap, $this->propertyRegistry);
                $beanMap->add($beanName, $createBean);

                unset($scannedServices[$key]);
            }
        }

        if (!empty($scannedServices)) {
            $loopCount++;
            if ($loopCount > self::MAX_LOOP_COUNT) {
                throw new CircularDependencyException("There is a circular dependency: ", $scannedServices);
            }

            $this->createBeanMap($scannedServices, $beanMap, $cachedDependencies, $loopCount);
        }
    }

    private function getDependencies(
        ServiceInjectionInfo $scannedService, array &$cachedDependencies, array $scannedServices): array
    {
        $containerKey = $scannedService->getContainerKey();

        if (array_key_exists($containerKey, $cachedDependencies)) {
            return $cachedDependencies[$containerKey];
        }

        $dependencies = [];
        foreach ($scannedService->getConstructorArgs() as $constructorArg) {
            if ($constructorArg->getType() === ConstructorInjectionArgType::PROPERTY) continue;

            if ($this->scannedServicesContainsServiceFromConstructorArg($constructorArg, $scannedServices)) {
                $dependencies[] = $constructorArg->getInjectionName();
            }
        }

        $cachedDependencies[$containerKey] = $dependencies;
        return $dependencies;
    }

    /**
     * @param ConstructorInjectionArg $constructorArg
     * @param ServiceInjectionInfo[] $scannedServices
     * @return bool
     */
    private function scannedServicesContainsServiceFromConstructorArg(
        ConstructorInjectionArg $constructorArg, array $scannedServices): bool
    {
        $type = $constructorArg->getParameter()->getType();
        if (!($type instanceof ReflectionNamedType)) {
            // No support for multi types (validated as well at scanning level)
            return false;
        }

        foreach ($scannedServices as $scannedService) {
            if ($constructorArg->hasQualifier() && $constructorArg->getQualifier() === $scannedService->getInjectionName()) {
                return true;
            }

            if(
                $type->getName() === $scannedService->getClass()->getName() ||
                ($scannedService->getBeanType() !== null && $type->getName() === $scannedService->getBeanType()->getName())
            ) {
                return true;
            }
        }

        return false;
    }

    private function areAllDependenciesResolved(Map $beanMap, array $dependencies): bool
    {
        foreach ($dependencies as $dependency) {
            $found = false;

            /**
             * @var string $containerKey
             * @var BeanInfo $bean
             */
            foreach ($beanMap->getAll() as $containerKey => $bean) {
                if ($containerKey === $dependency || in_array($dependency, $bean->getPossibleInjectionNames())) {
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                return false;
            }
        }

        return true;
    }
}