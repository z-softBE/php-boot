<?php

namespace PhpBoot\Event;

use PhpBoot\Event\Model\Event;

interface EventConsumer
{
    public function consumeEvent(Event $event): void;

    public function supports(string $eventId): bool;
}