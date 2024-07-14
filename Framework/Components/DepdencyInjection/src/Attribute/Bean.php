<?php

namespace PhpBoot\Di\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Bean
{
    public string|null $name;

    /**
     * @param string|null $name
     */
    public function __construct(string|null $name)
    {
        $this->name = $name;
    }


}