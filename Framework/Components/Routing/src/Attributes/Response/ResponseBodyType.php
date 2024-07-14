<?php

namespace PhpBoot\Http\Routing\Attributes\Response;

enum ResponseBodyType: string
{
    case RAW = 'RAW';
    case JSON = 'JSON';
    case XML = 'XML';
}
