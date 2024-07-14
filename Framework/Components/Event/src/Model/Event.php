<?php

namespace PhpBoot\Event\Model;

readonly class Event
{
    private string $id;
    private array $meteData;

    /**
     * @param string $id
     * @param array $meteData
     */
    public function __construct(string $id, array $meteData)
    {
        $this->id = $id;
        $this->meteData = $meteData;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getMeteData(): array
    {
        return $this->meteData;
    }

}