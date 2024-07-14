<?php

namespace PhpBoot\Http\Routing\Scan\Model;

use PhpBoot\Di\Container\BeanInfo;
use PhpBoot\Di\Container\ServiceContainerInterface;
use PhpBoot\Http\Common\HttpMethod;
use PhpBoot\Http\Routing\Attributes\Response\ResponseBody;
use PhpBoot\Http\Routing\Attributes\Response\ResponseStatus;
use PhpBoot\Http\Routing\Scan\Model\Builder\RouteInfoBuilder;
use Ramsey\Uuid\Uuid;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;

readonly class RouteInfo
{
    public static function fromArray(array $config, ServiceContainerInterface $container): self
    {
        $bean = $container->getBeanInfo($config['controllerBeanName']);
        $controllerClass = new ReflectionClass($config['controllerClass']);
        $method = $controllerClass->getMethod($config['methodName']);

        $builder = new RouteInfoBuilder();
        return $builder
            ->withRouteInfoId($config['routePathId'])
            ->withControllerBean($bean)
            ->withHttpMethod($config['httpMethod'])
            ->withPath(array_map(static fn($elem) => PathElement::fromArray($elem), $config['path']))
            ->withArguments(array_map(static fn($arg) => RouteArgument::fromArray($arg, $method), $config['arguments']))
            ->withResponseBody($config['responseBody'] !== null ? new ResponseBody($config['responseBody']['type'], $config['responseBody']['produces']) : null)
            ->withResponseStatus($config['responseStatus'] !== null ? new ResponseStatus($config['responseStatus']['statusCode']) : null)
            ->withMethod($method)
            ->withReturnType($method->getReturnType())
            ->build();
    }

    private string $routeInfoId;
    private BeanInfo $controllerBean;
    private HttpMethod $httpMethod;
    /** @var PathElement[]  */
    private array $path;
    /** @var RouteArgument[]  */
    private array $arguments;
    private ResponseBody|null $responseBody;
    private ResponseStatus|null $responseStatus;
    private ReflectionNamedType $returnType;
    private ReflectionMethod $method;

    /**
     * @param BeanInfo $controllerBean
     * @param HttpMethod $httpMethod
     * @param PathElement[] $path
     * @param RouteArgument[] $arguments
     * @param ResponseBody|null $responseBody
     * @param ResponseStatus|null $responseStatus
     * @param ReflectionNamedType $returnType
     * @param ReflectionMethod $method
     */
    public function __construct(
        BeanInfo            $controllerBean,
        HttpMethod          $httpMethod,
        array               $path,
        array               $arguments,
        ResponseBody|null   $responseBody,
        ResponseStatus|null $responseStatus,
        ReflectionNamedType $returnType,
        ReflectionMethod $method,
        string|null $routeInfoId = null
    )
    {
        $this->routeInfoId = $routeInfoId ?? Uuid::uuid4();
        $this->controllerBean = $controllerBean;
        $this->httpMethod = $httpMethod;
        $this->path = $path;
        $this->arguments = $arguments;
        $this->responseBody = $responseBody;
        $this->responseStatus = $responseStatus;
        $this->returnType = $returnType;
        $this->method = $method;
    }

    public function getRouteInfoId(): string
    {
        return $this->routeInfoId;
    }

    public function getControllerBean(): BeanInfo
    {
        return $this->controllerBean;
    }

    public function getHttpMethod(): HttpMethod
    {
        return $this->httpMethod;
    }

    /**
     * @return PathElement[]
     */
    public function getPath(): array
    {
        return $this->path;
    }

    /**
     * @return RouteArgument[]
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function getResponseBody(): ResponseBody|null
    {
        return $this->responseBody;
    }

    public function getResponseStatus(): ResponseStatus|null
    {
        return $this->responseStatus;
    }

    public function getReturnType(): ReflectionNamedType
    {
        return $this->returnType;
    }

    public function getMethod(): ReflectionMethod
    {
        return $this->method;
    }




}