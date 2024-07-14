<?php

namespace PhpBoot\Event;

use PhpBoot\Event\Model\Event;

class EventSystem
{
    /** @var EventConsumer[]  */
    private array $consumers = [];

    public function registerConsumer(string $consumerId, EventConsumer $consumer): void
    {
        $this->consumers[$consumerId] = $consumer;
    }

    public function removeConsumer(string $consumerId): void
    {
        if (array_key_exists($consumerId, $this->consumers)) {
            unset($this->consumers);
        }
    }

    public function sendEvent(Event $event): void
    {
        foreach ($this->consumers as $consumer) {
            if ($consumer->supports($event->getId())) {
                $consumer->consumeEvent($event);
            }
        }
    }
}