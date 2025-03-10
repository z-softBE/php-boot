<?php

namespace PhpBoot\Core;

use PhpBoot\Di\Cache\ServiceInjectionCacheGenerator;
use PhpBoot\Di\Container\ServiceContainer;
use PhpBoot\Di\Container\ServiceContainerInterface;
use PhpBoot\Di\Inject\ServiceCreator;
use PhpBoot\Di\Property\PropertiesReader;
use PhpBoot\Di\Property\PropertyRegistry;
use PhpBoot\Di\Scan\ServiceScanner;
use PhpBoot\Utils\FileSystemUtils;

abstract class Kernel
{
    protected PropertyRegistry $propertyRegistry;
    protected ServiceContainerInterface $serviceContainer;
    protected bool $production = false;

    public function __construct()
    {
        $this->createPropertyRegistry();

        $this->production = $this->propertyRegistry->getPropertyByNameOrDefault('production', false);

        $this->createContainer();
    }

    /**
     * @return string[]
     */
    protected abstract function getNamespacesToScan(): array;

    /**
     * @return string[]
     */
    protected abstract function getPsr4Mappings(): array;

    protected abstract function getPropertiesFilePath(): string;

    protected abstract function getRootDirectory(): string;

    protected abstract function getCacheDirectory(): string;

    protected function createPropertyRegistry(): void
    {
        $reader = new PropertiesReader();
        $properties = $reader->readProperties($this->getPropertiesFilePath());
        $properties['phpBoot']['rootDirectory'] = $this->getRootDirectory();
        $properties['phpBoot']['cacheDirectory'] = $this->getCacheDirectory();

        $this->propertyRegistry = new PropertyRegistry($properties);
    }

    protected function createContainer(): void
    {
        $cacheFile = $this->getCacheDirectory() . DIRECTORY_SEPARATOR . 'Di' . DIRECTORY_SEPARATOR . 'CachedServiceInjectionInfo.php';
        if ($this->production && FileSystemUtils::fileExists($cacheFile)) {
            require $cacheFile;

            $scannedServices = \Cache\PhpBoot\Di\CachedServiceInjectionInfo::getServiceInjectionInfoArray();
        } else {
            $scanner = new ServiceScanner($this->getPsr4Mappings(), $this->getNamespacesToScan());
            $scannedServices = $scanner->scan();

            $cacheGenerator = new ServiceInjectionCacheGenerator();
            $cacheGenerator->cacheServiceInjectionInfo($this->getCacheDirectory(), $scannedServices);
        }

        $serviceCreator = new ServiceCreator($this->propertyRegistry);
        $beanMap = $serviceCreator->createServices($scannedServices);

        $this->serviceContainer = new ServiceContainer($beanMap);
    }


}