<?php

namespace PhpBoot\Di\Inject;

use PhpBoot\Di\Container\BeanInfo;
use PhpBoot\Di\Exception\BeanCreationException;
use PhpBoot\Di\Exception\PropertyNotFoundException;
use PhpBoot\Di\Property\PropertyRegistry;
use PhpBoot\Di\Scan\Model\ConstructorInjectionArg;
use PhpBoot\Di\Scan\Model\ConstructorInjectionArgType;
use PhpBoot\Di\Scan\Model\ServiceInjectionInfo;
use PhpBoot\Utils\Structure\Map;
use ReflectionException;

final readonly class BeanFactory
{
    private function __construct()
    {

    }

    public static function createBean(
        ServiceInjectionInfo $injectionInfo, Map $beanMap, PropertyRegistry $propertyRegistry): BeanInfo
    {
        try {
            if ($injectionInfo->getBeanMethod() !== null) {
                $object = $injectionInfo->getBeanMethod()->invoke(
                    $beanMap->get($injectionInfo->getConfigurationContainerKey())->getService()
                );
            } else {
                $constructorArgs = [];
                foreach ($injectionInfo->getConstructorArgs() as $constructorArg) {
                    $constructorArgs[] = self::resolveConstructorArg(
                        $constructorArg, $beanMap, $propertyRegistry, $injectionInfo->getClass()->getName()
                    );
                }

                $object = empty($constructorArg) ?
                    $injectionInfo->getClass()->newInstance() :
                    $injectionInfo->getClass()->newInstance(...$constructorArgs);
            }

            return new BeanInfo(
                $injectionInfo->getServiceAttributeClassName(),
                $injectionInfo->getInjectionName(),
                $injectionInfo->isPrimary(),
                $injectionInfo->getContainerKey(),
                $object
            );
        } catch (ReflectionException $ex) {
            throw new BeanCreationException("Got a ReflectionException that is unrecoverable: {$ex->getMessage()}", 0, $ex);
        }
    }

    private static function resolveConstructorArg(
        ConstructorInjectionArg $constructorArg, Map $beanMap,
        PropertyRegistry        $propertyRegistry, string $serviceClassName): mixed
    {
        if ($constructorArg->getType() === ConstructorInjectionArgType::PROPERTY) {
            return self::resolveProperty($constructorArg, $propertyRegistry, $serviceClassName);
        }

        return self::resolveBean($constructorArg, $beanMap, $serviceClassName);
    }

    private static function resolveProperty(
        ConstructorInjectionArg $constructorArg, PropertyRegistry $propertyRegistry, string $serviceClassName
    ): string|int|float|bool|array|null
    {
        $propName = $constructorArg->getPropertyName();

        try {
            return $propertyRegistry->getPropertyByName($propName);
        } catch (PropertyNotFoundException $ex) {
            if ($constructorArg->hasDefaultValue()) {
                return $constructorArg->getParameter()->getDefaultValue();
            }

            if ($constructorArg->allowsNull()) {
                return null;
            }

            throw new BeanCreationException("Could not find property '{$propName}' for injection into '{$serviceClassName}'");
        }
    }

    private static function resolveBean(
        ConstructorInjectionArg $constructorArg, Map $beanMap, string $serviceClassName): mixed
    {
        $possibleInjections = [];

        /**
         * @var string $containerKey
         * @var BeanInfo $bean
         */
        foreach ($beanMap->getAll() as $containerKey => $bean) {
            // Find existing bean with qualifier
            if ($constructorArg->hasQualifier() && $bean->getInjectionName() !== null &&
                $bean->getInjectionName() === $constructorArg->getQualifier()) {
                return $bean->getService();
            }

            // Find existing bean based on class name
            if ($constructorArg->getParameterClassName() === $containerKey) {
                return $bean->getService();
            }

            if (in_array($constructorArg->getParameterClassName(), $bean->getPossibleInjectionNames())) {
                $possibleInjections[] = $bean;
            }
        }

        if (!empty($possibleInjections)) {
            $possibleInjectionsCount = count($possibleInjections);

            if ($possibleInjectionsCount === 1) {
                return $possibleInjections[0]->getService();
            }

            // Find primary
            foreach ($possibleInjections as $possibleInjection) {
                if ($possibleInjection->isPrimary()) return $possibleInjection->getService();
            }

            throw new BeanCreationException("We found {$possibleInjectionsCount} possible injections for bean 
            with type '{$constructorArg->getParameterClassName()}'. Did you forget to specify the Primary attribute?");
        }

        if ($constructorArg->hasDefaultValue()) {
            return $constructorArg->getParameter()->getDefaultValue();
        }

        if ($constructorArg->allowsNull()) {
            return null;
        }

        if ($constructorArg->hasQualifier()) {
            throw new BeanCreationException(
                "Can not inject bean with the name '{$constructorArg->getQualifier()}' into '{$serviceClassName}'. 
                Did you specify a service with this name?"
            );
        } else {
            throw new BeanCreationException(
                "Can not inject bean with the name '{$constructorArg->getParameterClassName()}' into '{$serviceClassName}'. 
                Did you add a Service attribute to this class?"
            );
        }
    }

}