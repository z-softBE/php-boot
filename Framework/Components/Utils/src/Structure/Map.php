<?php

namespace PhpBoot\Utils\Structure;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

/**
 * @template V
 */
class Map implements IteratorAggregate, Countable
{
    /** @var array<string|int, V>  */
    protected array $elements;

    /**
     * @param array<string|int, V> $elements
     */
    public function __construct(array $elements = [])
    {
        $this->elements = $elements;
    }

    public function isEmpty(): bool
    {
        return empty($this->elements);
    }

    /**
     * @param array<string|int, V> $elements
     * @return void
     */
    public function addAll(array $elements): void
    {
        $this->elements = array_merge($this->elements, $elements);
    }

    /**
     * @param string|int $key
     * @param V $value
     * @return void
     */
    public function add(string|int $key, mixed $value): void
    {
        $this->elements[$key] = $value;
    }

    public function has(string|int $key): bool
    {
        return array_key_exists($key, $this->elements);
    }

    /**
     * @return array<string|int, V>
     */
    public function getAll(): array
    {
        return $this->elements;
    }

    /**
     * @param string|int $key
     * @return V|null
     */
    public function get(string|int $key): mixed
    {
        return $this->elements[$key] ?? null;
    }

    public function remove(string|int $key): void
    {
        if ($this->has($key)) {
            unset($this->elements[$key]);
        }
    }

    public function clear(): void
    {
        $this->elements = [];
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->elements);
    }

    public function count(): int
    {
        return count($this->elements);
    }
}