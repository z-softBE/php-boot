<?php

namespace App\Services\InterfacesTest;

use PhpBoot\Di\Attribute\Service;

#[Service]
class CommonImplHello implements Common
{
    public function handle(): string
    {
        return "Hello";
    }
}