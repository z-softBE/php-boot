<?php

namespace PhpBoot\Security;

final class SecurityContextHolder
{
    private static self|null $instance = null;

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private Authentication|null $authentication;

    private function __construct()
    {
    }

    public function getAuthentication(): Authentication|null
    {
        return $this->authentication;
    }

    public function setAuthentication(Authentication $authentication): void
    {
        $this->authentication = $authentication;
    }

    public function clear(): void
    {
        $this->authentication = null;
    }

    public function isAuthenticated(): bool
    {
        return $this->authentication !== null && count($this->authentication->getAuthorities()) > 0;
    }

}