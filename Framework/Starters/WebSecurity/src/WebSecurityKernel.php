<?php

namespace PhpBoot\Starter\Web\Security;

use PhpBoot\Security\Cache\SecurityRuleCacheGenerator;
use PhpBoot\Security\Scan\Model\SecurityRule;
use PhpBoot\Security\Scan\SecurityRuleScanner;
use PhpBoot\Starter\Web\WebKernel;
use PhpBoot\Utils\FileSystemUtils;

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
        $cacheFile = $this->getCacheDirectory() . DIRECTORY_SEPARATOR . 'Security' . DIRECTORY_SEPARATOR . 'CachedSecurityRules.php';
        if ($this->production && FileSystemUtils::fileExists($cacheFile)) {
            require $cacheFile;
            $this->cachedSecurityRules = \Cache\PhpBoot\Security\CachedSecurityRules::getSecurityRules();

            return;
        }

        $scanner = new SecurityRuleScanner();
        $this->cachedSecurityRules = $scanner->scanForSecurityRules($this->cachedRouteInfo);

        $cacheGenerator = new SecurityRuleCacheGenerator();
        $cacheGenerator->cacheSecurityRules($this->getCacheDirectory(), $this->cachedSecurityRules);
    }

    private function registerSecurityEventConsumer(): void
    {
        $consumer = new SecurityEventConsumer($this->cachedSecurityRules);
        $this->eventSystem->registerConsumer('phpBoot.securityEventConsumer', $consumer);
    }
}