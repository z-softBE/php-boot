<?php

namespace PhpBoot\Di\Attribute;

use Attribute;

#[Attribute]
readonly class Property
{
    public string $name;

    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }


}