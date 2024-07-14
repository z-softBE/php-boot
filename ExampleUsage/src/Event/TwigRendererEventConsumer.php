<?php

namespace App\Event;

use PhpBoot\Di\Attribute\Service;
use PhpBoot\Event\EventConsumer;
use PhpBoot\Event\EventSystem;
use PhpBoot\Event\Model\Event;

#[Service]
class TwigRendererEventConsumer implements EventConsumer
{

    public function __construct(
        EventSystem $eventSystem
    )
    {
        $eventSystem->registerConsumer('twigRendererEventConsumer', $this);
    }

    public function consumeEvent(Event $event): void
    {
        var_dump($event);
        die;
        // TODO: Remove die statement in favor of logging
    }

    public function supports(string $eventId): bool
    {
        return $eventId === 'twigRenderer.render';
    }
}