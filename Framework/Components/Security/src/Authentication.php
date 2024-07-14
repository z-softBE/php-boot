<?php

namespace PhpBoot\Security;

readonly class Authentication
{
    /**
     * @var string[]
     */
    private array $authorities;
    private mixed $authenticationInfo;

    /**
     * @param string[] $authorities
     * @param mixed $authenticationInfo
     */
    public function __construct(array $authorities, mixed $authenticationInfo)
    {
        $this->authorities = $authorities;
        $this->authenticationInfo = $authenticationInfo;
    }

    public function getAuthorities(): array
    {
        return $this->authorities;
    }

    public function getAuthenticationInfo(): mixed
    {
        return $this->authenticationInfo;
    }

}