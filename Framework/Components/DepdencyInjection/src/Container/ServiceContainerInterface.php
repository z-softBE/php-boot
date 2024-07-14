<?php

namespace PhpBoot\Di\Container;

use Psr\Container\ContainerInterface;

interface ServiceContainerInterface extends ContainerInterface
{
    /**
     * @param string $attributeType
     * @return BeanInfo[]
     */
    public function getAllBeansByAttributeType(string $attributeType): array;

    public function getFirstBeanByClass(string $class): mixed;

    public function getBeanInfo(string $id): BeanInfo;
}