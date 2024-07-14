<?php

namespace App\Controllers;

use App\Model\Request\HelloRequest;
use App\Model\Response\HelloResponse;
use PhpBoot\Http\Common\HttpStatusCode;
use PhpBoot\Http\Request\Request;
use PhpBoot\Http\Routing\Attributes\Controller;
use PhpBoot\Http\Routing\Attributes\Path\GetPath;
use PhpBoot\Http\Routing\Attributes\Path\PathVariable;
use PhpBoot\Http\Routing\Attributes\Path\PostPath;
use PhpBoot\Http\Routing\Attributes\Request\HeaderParam;
use PhpBoot\Http\Routing\Attributes\Request\QueryParam;
use PhpBoot\Http\Routing\Attributes\Request\RequestBody;
use PhpBoot\Http\Routing\Attributes\Response\ResponseBody;
use PhpBoot\Http\Routing\Attributes\Response\ResponseBodyType;
use PhpBoot\Http\Routing\Attributes\Response\ResponseStatus;

#[Controller]
class HelloController
{
    #[GetPath(path: '/hello/world/{name}')]
    #[ResponseBody(type: ResponseBodyType::RAW, produces: 'text/html')]
    #[ResponseStatus(statusCode: HttpStatusCode::HTTP_CREATED)]
    public function hello(
        #[PathVariable] string                                                       $name,
        #[HeaderParam(name: 'Authorization', required: false)] string|null           $authHeader,
        #[QueryParam(name: 'capitalize', defaultValue: false, required: false)] bool $capitalize
    ): string
    {
        return "Hello world" . $name;
    }

    #[PostPath(path: '/hello/world')]
    #[ResponseBody(type: ResponseBodyType::JSON)]
    #[ResponseStatus(statusCode: HttpStatusCode::HTTP_CREATED)]
    public function helloPost(
        #[RequestBody(required: true)] HelloRequest $request,
        Request $httpRequest
    ): HelloResponse
    {
        return new HelloResponse($request->getName() . ' with response');
    }
}