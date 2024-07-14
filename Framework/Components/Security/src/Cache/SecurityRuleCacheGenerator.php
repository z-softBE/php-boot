<?php

namespace PhpBoot\Security\Cache;

use Nette\PhpGenerator\PhpNamespace;
use PhpBoot\Security\Scan\Model\SecurityRule;
use PhpBoot\Utils\FileSystemUtils;

class SecurityRuleCacheGenerator
{

    /**
     * @param string $cacheDirectory
     * @param SecurityRule[] $rules
     * @return void
     */
    public function cacheSecurityRules(string $cacheDirectory, array $rules): void
    {
        $rulesArray = array_map(fn(SecurityRule $rule) => $this->createSecurityRule($rule), $rules);

        $namespace = new PhpNamespace('Cache\\PhpBoot\\Security');
        $class = $namespace->addClass('CachedSecurityRules');
        $class->setFinal()
            ->setReadOnly();

        $class->addConstant('RULES', $rulesArray)
            ->setPrivate()
            ->setType('array');

        $class->addMethod('getSecurityRules')
            ->setFinal()
            ->setPublic()
            ->setStatic()
            ->setReturnType('array')
            ->addBody('$rules = [];')
            ->addBody('foreach (self::RULES as $rule) {')
            ->addBody('$rules[] = \\PhpBoot\\Security\\Scan\\Model\\SecurityRule::fromArray($rule);')
            ->addBody('}')
            ->addBody('return $rules;');

        $saveDirectory = $cacheDirectory . DIRECTORY_SEPARATOR . 'Security';
        FileSystemUtils::createDirectory($saveDirectory);

        file_put_contents(
            $saveDirectory . DIRECTORY_SEPARATOR . 'CachedSecurityRules.php',
            "<?php\n\n" . $namespace
        );
    }

    private function createSecurityRule(SecurityRule $rule): array
    {
        return [
            "routeId" => $rule->getRouteId(),
            "authorities" => $rule->getAuthorities()
        ];
    }
}