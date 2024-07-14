<?php

namespace App\Services\InterfacesTest;

use PhpBoot\Di\Attribute\Service;

#[Service(name: 'commonWithName')]
class CommonImplWithName implements Common
{
    public function handle(): string
    {
        return "withName";
    }
}