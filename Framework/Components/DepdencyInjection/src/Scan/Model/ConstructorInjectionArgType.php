<?php

namespace PhpBoot\Di\Scan\Model;

enum ConstructorInjectionArgType: string
{
    case BEAN = 'BEAN';
    case PROPERTY = 'PROPERTY';
}
