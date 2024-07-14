<?php

namespace App\Interceptor;

use PhpBoot\Di\Attribute\Service;
use PhpBoot\Http\Request\Request;
use PhpBoot\Http\Response\Response;
use PhpBoot\Starter\Web\Interceptor\InterceptorChain;
use PhpBoot\Starter\Web\Interceptor\PostRequestInterceptor;

#[Service]
class CustomPostRequestInterceptor extends PostRequestInterceptor
{
    public function __construct(
        InterceptorChain $interceptorChain
    )
    {
        $interceptorChain->registerInterceptor($this);
    }

    public function postRequest(Request $request, Response $response): void
    {
        $customRequestHeader = $request->getHeaders()->get('X-CustomRequest-interceptor');

        $response->getHeaders()->add('X-Custom-interceptor', 'Custom in response from request: ' . $customRequestHeader);
    }

    #[\Override] public function order(): int
    {
        return 100;
    }
}