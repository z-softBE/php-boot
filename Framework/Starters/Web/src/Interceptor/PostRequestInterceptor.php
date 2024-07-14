<?php

namespace PhpBoot\Starter\Web\Interceptor;

use PhpBoot\Http\Request\Request;

abstract class PostRequestInterceptor implements Interceptor
{
    public function preRequest(Request $request): void
    {
        //NOOP
    }

}