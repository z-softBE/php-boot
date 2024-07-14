<?php

namespace PhpBoot\Http\Common;

use PhpBoot\Utils\Structure\Map;

class HeaderMap extends Map
{
    public static function createFromServerGlobal(array $server): self
    {
        $map = new self();

        foreach ($server as $key => $value) {
            if (!str_starts_with($key, 'HTTP_')) continue;

            // Convert something like 'HTTP_USER_AGENT' to 'User-Agent'
            $name = ucwords(
                str_replace('_', '-', strtolower(substr($key, 5))),
                '-'
            );

            $map->add($name, $value);
        }

        return $map;
    }

    public static function createFromArray(array $headers): self
    {
        $map = new self();

        $map->addAll($headers);

        return $map;
    }

    public const string CONTENT_TYPE_HEADER = "Content-Type";
    public const string CONTENT_LENGTH_HEADER = "Content-Length";
    public const string ACCEPT_HEADER = "Accept";
    public const string AUTHORIZATION_HEADER = "Authorization";

    public function getContentType(): string|null
    {
        return $this->get(self::CONTENT_TYPE_HEADER);
    }

    public function getContentLength(): int|null
    {
        return $this->get(self::CONTENT_LENGTH_HEADER);
    }

    public function getAccept(): string|null
    {
        return $this->get(self::ACCEPT_HEADER);
    }

    public function getAuthorization(): string|null
    {
        return $this->get(self::AUTHORIZATION_HEADER);
    }

    #[\Override]
    public function addAll(array $elements): void
    {
        foreach ($elements as $key => $value) {
            $this->add($key, $value);
        }
    }


}