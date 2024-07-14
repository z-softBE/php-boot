<?php

namespace PhpBoot\Http\Routing\Scan\Model\Builder;

use PhpBoot\Di\Container\BeanInfo;
use PhpBoot\Http\Common\HttpMethod;
use PhpBoot\Http\Routing\Attributes\Response\ResponseBody;
use PhpBoot\Http\Routing\Attributes\Response\ResponseStatus;
use PhpBoot\Http\Routing\Scan\Model\PathElement;
use PhpBoot\Http\Routing\Scan\Model\RouteArgument;
use PhpBoot\Http\Routing\Scan\Model\RouteInfo;
use ReflectionMethod;
use ReflectionNamedType;

class RouteInfoBuilder
{
    private string|null $routeInfoId = null;
    private BeanInfo $controllerBean;
    private HttpMethod $httpMethod;
    /** @var PathElement[] */
    private array $path = [];
    /** @var RouteArgument[] */
    private array $arguments = [];
    private ResponseBody|null $responseBody;
    private ResponseStatus|null $responseStatus;
    private ReflectionNamedType $returnType;
    private ReflectionMethod $method;

    public function withRouteInfoId(string|null $routeInfoId): self
    {
        $this->routeInfoId = $routeInfoId;
        return $this;
    }

    public function withControllerBean(BeanInfo $controllerBean): self
    {
        $this->controllerBean = $controllerBean;
        return $this;
    }

    public function withHttpMethod(HttpMethod $method): self
    {
        $this->httpMethod = $method;
        return $this;
    }

    /**
     * @param PathElement[] $path
     * @return $this
     */
    public function withPath(array $path): self
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @param RouteArgument[] $arguments
     * @return $this
     */
    public function withArguments(array $arguments): self
    {
        $this->arguments = $arguments;
        return $this;
    }

    public function withResponseBody(ResponseBody|null $responseBody): self
    {
        $this->responseBody = $responseBody;
        return $this;
    }

    public function withResponseStatus(ResponseStatus|null $responseStatus): self
    {
        $this->responseStatus = $responseStatus;
        return $this;
    }

    public function withReturnType(ReflectionNamedType $returnType): self
    {
        $this->returnType = $returnType;
        return $this;
    }

    public function withMethod(ReflectionMethod $method): self
    {
        $this->method = $method;
        return $this;
    }

    public function build(): RouteInfo
    {
        return new RouteInfo(
            $this->controllerBean,
            $this->httpMethod,
            $this->path,
            $this->arguments,
            $this->responseBody,
            $this->responseStatus,
            $this->returnType,
            $this->method,
            $this->routeInfoId
        );
    }
}