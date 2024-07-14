<?php

namespace App\Interceptor;

use PhpBoot\Di\Attribute\Service;
use PhpBoot\Http\Request\Request;
use PhpBoot\Starter\Web\Interceptor\InterceptorChain;
use PhpBoot\Starter\Web\Interceptor\PreRequestInterceptor;

#[Service]
class CustomPreRequestInterceptor extends PreRequestInterceptor
{

    public function __construct(
        InterceptorChain $interceptorChain
    )
    {
        $interceptorChain->registerInterceptor($this);
    }

    #[\Override] public function preRequest(Request $request): void
    {
        $request->getHeaders()->add('X-CustomRequest-interceptor', 'This is set in a PRE REQUEST interceptor');
    }

    #[\Override] public function order(): int
    {
        return 100;
    }
}