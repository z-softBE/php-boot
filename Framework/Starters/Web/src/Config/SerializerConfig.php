<?php

namespace PhpBoot\Starter\Web\Config;

use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use PhpBoot\Di\Attribute\Bean;
use PhpBoot\Di\Attribute\Configuration;

#[Configuration]
class SerializerConfig
{
    #[Bean]
    public function jmsSerializer(): Serializer
    {
        return SerializerBuilder::create()->build();
    }
}