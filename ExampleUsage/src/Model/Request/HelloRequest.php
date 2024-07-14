<?php

namespace App\Model\Request;

readonly class HelloRequest
{
    public function __construct(
        private string $name
    )
    {
    }

    public function getName(): string
    {
        return $this->name;
    }


}