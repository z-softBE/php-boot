<?php

namespace PhpBoot\Starter\Web\Security;

use PhpBoot\Security\Scan\Model\SecurityRule;
use PhpBoot\Security\Scan\SecurityRuleScanner;
use PhpBoot\Starter\Web\WebKernel;

abstract class WebSecurityKernel extends WebKernel
{
    /** @var SecurityRule[] */
    protected array $cachedSecurityRules;

    public function __construct()
    {
        parent::__construct();

        $this->createSecurityRules();
        $this->registerSecurityEventConsumer();
    }

    private function createSecurityRules(): void
    {
        $scanner = new SecurityRuleScanner();
        $this->cachedSecurityRules = $scanner->scanForSecurityRules($this->cachedRouteInfo);
    }

    private function registerSecurityEventConsumer(): void
    {
        $consumer = new SecurityEventConsumer($this->cachedSecurityRules);
        $this->eventSystem->registerConsumer('phpBoot.securityEventConsumer', $consumer);
    }
}