<?php

namespace PhpBoot\Http\Routing\Attributes\Path;

use Attribute;
use PhpBoot\Http\Common\HttpMethod;

#[Attribute(Attribute::TARGET_METHOD)]
readonly class Path
{
    public function __construct(
        public string $path,
        public HttpMethod $method
    )
    {

    }
}