<?php

namespace PhpBoot\Http\Request;

final readonly class RequestFactory
{
    private function __construct()
    {

    }

    public static function createFromGlobals(): Request
    {
        return new Request($_SERVER, $_GET, $_POST, $_FILES, $_COOKIE);
    }
}