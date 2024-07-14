<?php

namespace PhpBoot\Http\Common;

enum HttpProtocolVersion: string
{
    case HTTP_1_1 = '1.1';
    case HTTP_2_0 = '2.0';
}