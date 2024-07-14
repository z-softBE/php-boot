<?php

namespace PhpBoot\Http\Common;

use UnexpectedValueException;

enum HttpMethod: string
{
    case GET = 'GET';
    case HEAD = 'HEAD';
    case POST = 'POST';
    case PUT = 'PUT';
    case DELETE = 'DELETE';
    case CONNECT = 'CONNECT';
    case OPTIONS = 'OPTIONS';
    case TRACE = 'TRACE';
    case PATCH = 'PATCH';

    public static function fromString(string $method): self
    {
        foreach (self::cases() as $httpMethod) {
            if ($httpMethod->value === strtoupper($method)) {
                return $httpMethod;
            }
        }

        throw new UnexpectedValueException("Could not match HTTP Method: {$method}");
    }
}
