<?php

namespace PhpBoot\Data\Attribute;

use Attribute;
use PhpBoot\Di\Attribute\Service;

#[Attribute(Attribute::TARGET_CLASS)]
readonly class Repository extends Service
{
    public function __construct(string|null $name = null)
    {
        parent::__construct($name);
    }
}