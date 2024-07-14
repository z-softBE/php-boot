<?php

namespace PhpBoot\Di\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
readonly class Configuration extends Service
{
    public function __construct(string|null $name)
    {
        parent::__construct($name);
    }

}