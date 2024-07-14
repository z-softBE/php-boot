<?php

namespace PhpBoot\Http\Routing\Attributes\Path;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
readonly class PathVariable
{
    public function __construct(
        public string|null $name = null
    )
    {

    }
}