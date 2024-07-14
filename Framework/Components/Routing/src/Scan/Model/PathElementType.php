<?php

namespace PhpBoot\Http\Routing\Scan\Model;

enum PathElementType: string
{
    case NORMAL = 'NORMAL';
    case VARIABLE = 'VARIABLE';
}