<?php

namespace App\Services;

use App\Services\InterfacesTest\Common;
use PhpBoot\Di\Attribute\Qualifier;
use PhpBoot\Di\Attribute\Service;

#[Service]
readonly class Service1
{
    public function __construct(
        private Common $common,
        #[Qualifier(name: 'commonWithName')] private Common $commonWithName
    )
    {
    }

    public function getCommon(): Common
    {
        return $this->common;
    }

    public function getCommonWithName(): Common
    {
        return $this->commonWithName;
    }


}