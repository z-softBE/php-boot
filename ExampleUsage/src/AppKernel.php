<?php

namespace App;

use PhpBoot\Starter\Web\WebKernel;

class AppKernel extends WebKernel
{

    #[\Override] protected function getNamespacesToScan(): array
    {
        return [
            'App',
            'PhpBoot\\Starter\\Web\\',
            'PhpBoot\\Starter\\DoctrineORM\\',
            'PhpBoot\\Starter\\Twig\\',
            'PhpBoot\\Starter\\Event\\'
        ];
    }

    #[\Override] protected function getPsr4Mappings(): array
    {
        return require dirname(__DIR__) . '/vendor/composer/autoload_psr4.php';
    }

    #[\Override] protected function getPropertiesFilePath(): string
    {
        return dirname(__DIR__) . '/config/properties.yaml';
    }

    #[\Override] protected function getRootDirectory(): string
    {
        return dirname(__DIR__);
    }

    #[\Override] protected function getCacheDirectory(): string
    {
        return dirname(__DIR__) . '/cache';
    }
}