<?php

namespace PhpBoot\Starter\Web\Interceptor;

use PhpBoot\Di\Attribute\Service;
use PhpBoot\Http\Request\Request;
use PhpBoot\Http\Response\Response;

#[Service(name: 'phpBoot.interceptorChain')]
class InterceptorChain
{
    /** @var Interceptor[]  */
    private array $interceptors = [];

    public function registerInterceptor(Interceptor $interceptor): void
    {
        $this->interceptors[] = $interceptor;
        usort($this->interceptors, static fn(Interceptor $a, Interceptor $b) => $a->order() - $b->order());
    }

    public function preRequest(Request $request): void
    {
        foreach ($this->interceptors as $interceptor) {
            $interceptor->preRequest($request);
        }
    }

    public function postRequest(Request $request, Response $response): void
    {
        foreach ($this->interceptors as $interceptor) {
            $interceptor->postRequest($request, $response);
        }
    }
}