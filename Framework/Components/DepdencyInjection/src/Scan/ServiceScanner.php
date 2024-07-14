<?php

namespace PhpBoot\Di\Scan;

use PhpBoot\Di\Attribute\Configuration;
use PhpBoot\Di\Attribute\Primary;
use PhpBoot\Di\Attribute\Property;
use PhpBoot\Di\Attribute\Qualifier;
use PhpBoot\Di\Attribute\Service;
use PhpBoot\Di\Common\ReflectionTypeValidator;
use PhpBoot\Di\Exception\ReflectionTypeException;
use PhpBoot\Di\Exception\ServiceScannerException;
use PhpBoot\Di\Scan\Model\Builder\ConstructorInjectionArgBuilder;
use PhpBoot\Di\Scan\Model\Builder\ServiceInjectionInfoBuilder;
use PhpBoot\Di\Scan\Model\ConstructorInjectionArg;
use PhpBoot\Di\Scan\Model\ConstructorInjectionArgType;
use PhpBoot\Di\Scan\Model\ServiceInjectionInfo;
use PhpBoot\Utils\ArrayUtils;
use PhpBoot\Utils\StringUtils;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionParameter;

class ServiceScanner
{
    private array $psr4Mappings;
    /** @var string[]  */
    private array $baseNamespacesToScan;
    private ConfigurationScanner $configurationScanner;

    /**
     * @param array $psr4Mappings
     * @param string[] $baseNamespacesToScan
     */
    public function __construct(array $psr4Mappings, array $baseNamespacesToScan)
    {
        $this->psr4Mappings = $psr4Mappings;
        $this->baseNamespacesToScan = $baseNamespacesToScan;
        $this->configurationScanner = new ConfigurationScanner();
    }

    /**
     * @return ServiceInjectionInfo[]
     * @throws ServiceScannerException
     * @throws ReflectionTypeException
     */
    public function scan(): array
    {
        $services = [];
        foreach ($this->getClassesToScan() as $class) {
            $serviceAttributes = $class->getAttributes(Service::class, ReflectionAttribute::IS_INSTANCEOF);
            $serviceAttributesCount = count($serviceAttributes);

            if ($serviceAttributesCount === 0) continue;

            if ($serviceAttributesCount > 1) {
                throw new ServiceScannerException("The service '{$class->getName()}' has multiple Service attributes.
                A class can only have one!");
            }

            /** @var ReflectionAttribute $serviceAttribute */
            $serviceAttribute = ArrayUtils::getFirstElement($serviceAttributes);
            $serviceInjectionInfo = $this->buildServiceInjectionInfo($class, $serviceAttribute);
            $services[] = $serviceInjectionInfo;

            if ($serviceAttribute->getName() === Configuration::class) {
                $services = array_merge($services, $this->configurationScanner->scanConfiguration($class, $serviceInjectionInfo->getContainerKey()));
            }
        }
        return $services;
    }

    /**
     * @return ReflectionClass[]
     * @throws ServiceScannerException
     */
    private function getClassesToScan(): array
    {
        $classes = [];
        foreach ($this->baseNamespacesToScan as $namespace) {
            $classes = array_merge($classes, $this->scanNamespace($namespace));
        }
        return $classes;
    }

    /**
     * @param string $namespace
     * @return ReflectionClass[]
     * @throws ServiceScannerException
     */
    private function scanNamespace(string $namespace): array
    {
        $classes = [];
        foreach ($this->findStartingDirectories($namespace) as $directory) {
            $classes = array_merge($classes, FileScanner::scanForClassesInNamespace($namespace, $directory, true));
        }
        return $classes;
    }

    /**
     * @param string $namespace
     * @return string[]
     * @throws ServiceScannerException
     */
    private function findStartingDirectories(string $namespace): array
    {
        $namespace = StringUtils::addTrailingString($namespace, '\\');

        // Namespace matches completely
        if (array_key_exists($namespace, $this->psr4Mappings)) {
            return $this->psr4Mappings[$namespace];
        }

        // Find parent psr4 mapping and append the path
        foreach ($this->psr4Mappings as $key => $directories) {
            if (!str_starts_with($namespace, $key)) continue;

            $pathToAppend = StringUtils::addBeginningString(
                ltrim(rtrim($namespace, '\\'), $key),
                DIRECTORY_SEPARATOR
            );

            return array_map(static fn($directory) => $directory . $pathToAppend, $directories);
        }

        throw new ServiceScannerException("Could not find any PSR-4 mapping for namespace '{$namespace}'. Is there a typo?");
    }

    private function buildServiceInjectionInfo(ReflectionClass $class, ReflectionAttribute $serviceAttribute): ServiceInjectionInfo
    {
        $builder = new ServiceInjectionInfoBuilder();
        return $builder
            ->withClass($class)
            ->withInjectionName($this->getInjectionName($serviceAttribute))
            ->withServiceAttributeClassName($serviceAttribute->getName())
            ->withPrimary($this->hasPrimaryAttribute($class))
            ->withConstructorArgs($this->getConstructorInjectionArgs($class))
            ->build();
    }

    private function getInjectionName(ReflectionAttribute $serviceAttribute): string|null
    {
        /** @var Service $service */
        $service = $serviceAttribute->newInstance();

        if (!StringUtils::isBlank($service->name)) return $service->name;

        return null;
    }

    private function hasPrimaryAttribute(ReflectionClass $class): bool
    {
        $primaryAttributes = $class->getAttributes(Primary::class, ReflectionAttribute::IS_INSTANCEOF);
        return count($primaryAttributes) > 0;
    }

    /**
     * @param ReflectionClass $class
     * @return ConstructorInjectionArg[]
     * @throws ReflectionTypeException
     */
    private function getConstructorInjectionArgs(ReflectionClass $class): array
    {
        $constructor = $class->getConstructor();

        if ($constructor === null) return [];

        $args = [];
        foreach ($constructor->getParameters() as $parameter) {
            ReflectionTypeValidator::validateConstructorParameterType($parameter, $class->getName());
            $args[] = $this->buildConstructorArg($parameter);
        }
        return $args;
    }

    private function buildConstructorArg(ReflectionParameter $parameter): ConstructorInjectionArg
    {
        $builder = new ConstructorInjectionArgBuilder();
        return $builder
            ->withParameter($parameter)
            ->withType($this->getConstructorInjectionArgType($parameter))
            ->withQualifier($this->getConstructorInjectionArgQualifier($parameter))
            ->build();
    }

    private function getConstructorInjectionArgType(ReflectionParameter $parameter): ConstructorInjectionArgType
    {
        $propertyAttributes = $parameter->getAttributes(Property::class, ReflectionAttribute::IS_INSTANCEOF);

        if (empty($propertyAttributes)) return ConstructorInjectionArgType::BEAN;

        return ConstructorInjectionArgType::PROPERTY;
    }

    private function getConstructorInjectionArgQualifier(ReflectionParameter $parameter): string|null
    {
        $qualifierAttributes = $parameter->getAttributes(Qualifier::class, ReflectionAttribute::IS_INSTANCEOF);

        if (empty($qualifierAttributes)) return  null;

        return ArrayUtils::getFirstElement($qualifierAttributes)->newInstance()->name;
    }


}