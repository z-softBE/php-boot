<?php

namespace PhpBoot\Http\Routing\Attributes\Request;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
readonly class HeaderParam
{
    public function __construct(
        public string|null $name = null,
        public string|int|float|null $defaultValue = null,
        public bool $required = false
    )
    {

    }
}