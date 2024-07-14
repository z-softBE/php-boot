<?php

namespace App\Services\InterfacesTest;

use PhpBoot\Di\Attribute\Primary;
use PhpBoot\Di\Attribute\Service;

#[Service]
#[Primary]
class CommonImplWorld implements Common
{

    public function handle(): string
    {
        return "world";
    }
}