<?php

namespace App\Services;

use PhpBoot\Di\Attribute\Property;
use PhpBoot\Di\Attribute\Service;

#[Service]
readonly class Service2
{
    public function __construct(
        private Service1 $service1,
        #[Property(name: 'hello.world')] private string $helloWorld,
        private string|null $test1Nullable,
        private string|null $test2Nullable = null,
        private string $withDefault = 'withDefault'
    )
    {

    }

    public function getService1(): Service1
    {
        return $this->service1;
    }

    public function getHelloWorld(): string
    {
        return $this->helloWorld;
    }

    public function getTest1Nullable(): string|null
    {
        return $this->test1Nullable;
    }

    public function getTest2Nullable(): string|null
    {
        return $this->test2Nullable;
    }

    public function getWithDefault(): string
    {
        return $this->withDefault;
    }



}