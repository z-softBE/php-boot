<?php

namespace PhpBoot\Http\Routing\Scan\Model;

readonly class PathElement
{
    public static function fromArray(array $config): self
    {
        return new self($config['type'], $config['value']);
    }

    private PathElementType $type;
    private string $value;

    /**
     * @param PathElementType $type
     * @param string $value
     */
    public function __construct(PathElementType $type, string $value)
    {
        $this->type = $type;
        $this->value = $value;
    }

    public function getType(): PathElementType
    {
        return $this->type;
    }

    public function getValue(): string
    {
        return $this->value;
    }

}