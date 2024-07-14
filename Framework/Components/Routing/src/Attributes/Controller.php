<?php

namespace PhpBoot\Http\Routing\Attributes;

use Attribute;
use PhpBoot\Di\Attribute\Service;

#[Attribute(Attribute::TARGET_CLASS)]
readonly class Controller extends Service
{
    public function __construct(string|null $name = null)
    {
        parent::__construct($name);
    }
}