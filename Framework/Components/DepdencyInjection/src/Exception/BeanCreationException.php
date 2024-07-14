<?php

namespace PhpBoot\Di\Exception;

use Exception;
use Psr\Container\ContainerExceptionInterface;

class BeanCreationException extends Exception implements ContainerExceptionInterface
{

}