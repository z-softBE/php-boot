<?php

namespace PhpBoot\Http\Cookie;

enum SameSite: string
{
    case LAX = 'Lax';
    case STRICT = 'Strict';
    case NONE = 'None';
}
