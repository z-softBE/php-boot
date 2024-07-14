<?php

namespace PhpBoot\Security\Scan\Model;

readonly class SecurityRule
{
    public static function fromArray(array $config): self
    {
        return new self($config['routeId'], $config['authorities']);
    }

    private string $routeId;
    /** @var string[]  */
    private array $authorities;

    /**
     * @param string $routeId
     * @param string[] $authorities
     */
    public function __construct(string $routeId, array $authorities)
    {
        $this->routeId = $routeId;
        $this->authorities = $authorities;
    }

    public function getRouteId(): string
    {
        return $this->routeId;
    }

    public function getAuthorities(): array
    {
        return $this->authorities;
    }

}