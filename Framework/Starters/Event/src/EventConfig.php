<?php

namespace PhpBoot\Starter\Event;

use PhpBoot\Di\Attribute\Bean;
use PhpBoot\Di\Attribute\Configuration;
use PhpBoot\Event\EventSystem;

#[Configuration]
class EventConfig
{

    #[Bean(name: 'phpBoot.eventSystem')]
    public function eventSystem(): EventSystem
    {
        return new EventSystem();
    }
}