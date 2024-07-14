<?php

namespace PhpBoot\Di\Exception;

use Exception;
use Psr\Container\ContainerExceptionInterface;

class BeanNotFoundException extends Exception implements ContainerExceptionInterface
{

}