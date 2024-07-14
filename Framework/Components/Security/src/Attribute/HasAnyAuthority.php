<?php

namespace PhpBoot\Security\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
readonly class HasAnyAuthority
{
    /**
     * @param string[] $authorities
     */
    public function __construct(
        public array $authorities
    )
    {

    }
}