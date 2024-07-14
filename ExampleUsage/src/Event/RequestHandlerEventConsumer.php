<?php

namespace App\Event;

use Monolog\Logger;
use PhpBoot\Di\Attribute\Service;
use PhpBoot\Event\EventConsumer;
use PhpBoot\Event\EventSystem;
use PhpBoot\Event\Model\Event;

#[Service]
class RequestHandlerEventConsumer implements EventConsumer
{
    public function __construct(
        EventSystem $eventSystem,
        private Logger $logger
    )
    {
        $eventSystem->registerConsumer(self::class, $this);
    }

    public function consumeEvent(Event $event): void
    {
        $this->logger->info("Received event: {$event->getId()}");
    }

    public function supports(string $eventId): bool
    {
        return $eventId === 'handleRequest.start' || $eventId === 'handleRequest.sendResponse';
    }
}