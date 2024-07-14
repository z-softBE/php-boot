<?php

namespace PhpBoot\Starter\Web\Interceptor;

use PhpBoot\Http\Request\Request;
use PhpBoot\Http\Response\Response;

interface Interceptor
{
    public function preRequest(Request $request): void;

    public function postRequest(Request $request, Response $response): void;

    public function order(): int;
}