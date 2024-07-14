<?php

namespace PhpBoot\Di\Scan\Model\Builder;

use PhpBoot\Di\Attribute\Qualifier;
use PhpBoot\Di\Scan\Model\ConstructorInjectionArg;
use PhpBoot\Di\Scan\Model\ConstructorInjectionArgType;
use ReflectionParameter;

class ConstructorInjectionArgBuilder
{

    private ReflectionParameter $parameter;
    private ConstructorInjectionArgType $type;
    private string|null $qualifier = null;

    public function withParameter(ReflectionParameter $parameter): self
    {
        $this->parameter = $parameter;
        return $this;
    }

    public function withType(ConstructorInjectionArgType $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function withQualifier(string|null $qualifier): self
    {
        $this->qualifier = $qualifier;
        return $this;
    }

    public function build(): ConstructorInjectionArg
    {
        return new ConstructorInjectionArg(
            $this->parameter,
            $this->type,
            $this->qualifier
        );
    }
}