<?php

namespace App\Event;

use Monolog\Logger;
use PhpBoot\Di\Attribute\Service;
use PhpBoot\Event\EventConsumer;
use PhpBoot\Event\EventSystem;
use PhpBoot\Event\Model\Event;

#[Service]
class TwigRendererEventConsumer implements EventConsumer
{

    public function __construct(
        EventSystem $eventSystem,
        private Logger $logger
    )
    {
        $eventSystem->registerConsumer('twigRendererEventConsumer', $this);
    }

    public function consumeEvent(Event $event): void
    {
        $this->logger->info("Rendering template: {$event->getMeteData()['path']}");
    }

    public function supports(string $eventId): bool
    {
        return $eventId === 'twigRenderer.render';
    }
}