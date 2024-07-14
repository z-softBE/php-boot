<?php

namespace PhpBoot\Starter\Web\Config;

use PhpBoot\Di\Attribute\Bean;
use PhpBoot\Di\Attribute\Configuration;
use PhpBoot\Exception\ExceptionHandlerDelegator;

#[Configuration]
class ExceptionConfig
{

    #[Bean(name: 'phpBoot.exceptionHandlerDelegator')]
    public function exceptionHandlerDelegator(): ExceptionHandlerDelegator
    {
        return new ExceptionHandlerDelegator();
    }
}