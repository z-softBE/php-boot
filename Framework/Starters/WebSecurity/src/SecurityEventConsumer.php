<?php

namespace PhpBoot\Starter\Web\Security;

use PhpBoot\Event\EventConsumer;
use PhpBoot\Event\Model\Event;
use PhpBoot\Security\Exception\ForbiddenException;
use PhpBoot\Security\Exception\UnauthorizedException;
use PhpBoot\Security\Scan\Model\SecurityRule;
use PhpBoot\Security\SecurityContextHolder;

readonly class SecurityEventConsumer implements EventConsumer
{

    /** @var SecurityRule[]  */
    private array $securityRules;

    /**
     * @param SecurityRule[] $securityRules
     */
    public function __construct(array $securityRules)
    {
        $this->securityRules = $securityRules;
    }


    public function consumeEvent(Event $event): void
    {
        $routeId = $event->getMeteData()['routeId'];

        if (!isset($this->securityRules[$routeId])) return;

        if (!SecurityContextHolder::getInstance()->isAuthenticated()) {
            throw new UnauthorizedException("You are not authenticated");
        }

        $rule = $this->securityRules[$routeId];
        $authenticatedAuthorities = SecurityContextHolder::getInstance()->getAuthentication()->getAuthorities();

        if (count(array_intersect($rule->getAuthorities(), $authenticatedAuthorities)) <= 0) {
            throw new ForbiddenException("You are not permitted to call this route");
        }
    }

    public function supports(string $eventId): bool
    {
        return $eventId === 'router.routeFound';
    }
}