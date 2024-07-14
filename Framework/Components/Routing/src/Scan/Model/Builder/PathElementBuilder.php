<?php

namespace PhpBoot\Http\Routing\Scan\Model\Builder;

use PhpBoot\Http\Routing\Scan\Model\PathElement;
use PhpBoot\Http\Routing\Scan\Model\PathElementType;

class PathElementBuilder
{
    private PathElementType $type;
    private string $value;

    public function withType(PathElementType $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function withValue(string $value): self
    {
        $this->value = $value;
        return $this;
    }

    public function build(): PathElement
    {
        return new PathElement($this->type, $this->value);
    }
}