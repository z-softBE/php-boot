<?php

namespace App\Model\Response;

readonly class HelloResponse
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