<?php

namespace PhpBoot\Security\Scan;

use PhpBoot\Http\Routing\Scan\Model\RouteInfo;
use PhpBoot\Security\Attribute\HasAnyAuthority;
use PhpBoot\Security\Scan\Model\SecurityRule;
use PhpBoot\Utils\ArrayUtils;
use ReflectionAttribute;

readonly class SecurityRuleScanner
{
    /**
     * @param RouteInfo[] $routes
     * @return SecurityRule[]
     */
    public function scanForSecurityRules(array $routes): array
    {
        $rules = [];

        foreach ($routes as $route) {
            /** @var ReflectionAttribute|null $attribute */
            $attribute = ArrayUtils::getFirstElement(
                $route->getMethod()->getAttributes(HasAnyAuthority::class, ReflectionAttribute::IS_INSTANCEOF)
            );

            if ($attribute === null) continue;

            $authorities = $attribute->newInstance()->authorities;

            $rules[$route->getRouteInfoId()] = new SecurityRule($route->getRouteInfoId(), $authorities);
        }

        return $rules;
    }
}