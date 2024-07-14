<?php

namespace PhpBoot\Di\Property;

use PhpBoot\Di\Exception\PropertyNotFoundException;

readonly class PropertyRegistry
{
    private const string NAME_SEPARATOR = '.';

    private array $properties;

    /**
     * @param array $properties
     */
    public function __construct(array $properties)
    {
        $this->properties = $properties;
    }

    public function getPropertyByName(string $name): string|int|bool|float|array
    {
        $result = $this->tryToGetPropertyByName($name);

        if ($result === null) {
            throw new PropertyNotFoundException("Could not find property with name: {$name}");
        }

        return $result;
    }

    public function getPropertyByNameOrDefault(string $name, string|int|bool|float|array|null $defaultValue): string|int|bool|float|array
    {
        $result = $this->tryToGetPropertyByName($name);

        if ($result === null) return $defaultValue;

        return $result;
    }

    private function tryToGetPropertyByName(string $name): string|int|bool|float|array|null
    {
        $keys = explode(self::NAME_SEPARATOR, $name);

        $currentValue = $this->properties;
        foreach ($keys as $key) {
            if (!array_key_exists($key, $currentValue)) return null;

            $currentValue = $currentValue[$key];
        }

        return $currentValue;
    }

}