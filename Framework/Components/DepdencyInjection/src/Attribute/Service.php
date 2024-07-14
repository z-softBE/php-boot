<?php

namespace PhpBoot\Di\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
readonly class Service
{
    public string|null $name;

    /**
     * @param string|null $name
     */
    public function __construct(string|null $name = null)
    {
        $this->name = $name;
    }


}