<?php

namespace PhpBoot\Starter\Web\Interceptor;

use PhpBoot\Http\Request\Request;
use PhpBoot\Http\Response\Response;

abstract class PreRequestInterceptor implements Interceptor
{
    public function postRequest(Request $request, Response $response): void
    {
        // NOOP
    }

}