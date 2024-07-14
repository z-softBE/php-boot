<?php

namespace PhpBoot\Http\Routing\Attributes\Request;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
readonly class RequestBody
{
    public function __construct(
        public bool $required = false
    )
    {
    }
}