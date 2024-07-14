<?php

namespace PhpBoot\Di\Property;

use Symfony\Component\Yaml\Yaml;

readonly class PropertiesReader
{
    public function readProperties(string $filePath): array
    {
        return Yaml::parse(file_get_contents($filePath));
    }
}