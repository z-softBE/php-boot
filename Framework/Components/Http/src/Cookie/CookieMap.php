<?php

namespace PhpBoot\Http\Cookie;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use PhpBoot\Utils\StringUtils;
use Traversable;

class CookieMap implements Countable, IteratorAggregate
{

    public static function createFromCookieGlobal(array $cookieGlobal): self
    {
        $cookies = [];
        foreach ($cookieGlobal as $name => $value) {
            $cookies[] = new Cookie($name, $value);
        }

        return new self($cookies);
    }

    /** @var array<string,Cookie>  */
    private array $cookies = [];

    /**
     * @param Cookie[] $cookies
     */
    public function __construct(array $cookies = [])
    {
        $this->addAll($cookies);
    }

    /**
     * @param Cookie[] $cookies
     * @return void
     */
    public function addAll(array $cookies): void
    {
        foreach ($cookies as $cookie) {
            $this->add($cookie);
        }
    }

    public function add(Cookie $cookie): void
    {
        $this->cookies[$cookie->getId()] = $cookie;
    }

    public function get(string $name, string|null $domain = null, string|null $path = null): Cookie|null
    {
        $checkDomain = !StringUtils::isBlank($domain);
        $checkPath = !StringUtils::isBlank($path);

        foreach ($this->cookies as $cookie) {
            if ($name !== $cookie->getName()) continue;

            if ($checkDomain && $domain !== $cookie->getDomain()) continue;

            if ($checkPath && $path !== $cookie->getPath()) continue;

            return $cookie;
        }

        return null;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->cookies);
    }

    public function count(): int
    {
        return count($this->cookies);
    }
}