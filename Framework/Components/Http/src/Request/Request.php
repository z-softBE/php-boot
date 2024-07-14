<?php

namespace PhpBoot\Http\Request;

use PhpBoot\Http\Common\HeaderMap;
use PhpBoot\Http\Common\HttpMethod;
use PhpBoot\Http\Cookie\CookieMap;
use PhpBoot\Http\File\FileMap;
use PhpBoot\Utils\StringUtils;
use PhpBoot\Utils\Structure\Map;

readonly class Request
{
    protected HeaderMap $headers;
    protected Map $query;
    protected Map $post;
    protected Map $server;
    protected CookieMap $cookies;
    protected FileMap $files;
    protected HttpMethod $method;
    protected string $protocol;
    protected string $requestUri;
    protected float $requestStartTime;
    protected string|null $rawContent;

    public function __construct(array $server, array $get, array $post, array $files, array $cookies)
    {
        $this->server = new Map($server);
        $this->query = new Map($get);
        $this->post = new Map($post);
        $this->cookies = CookieMap::createFromCookieGlobal($cookies);
        $this->headers = HeaderMap::createFromServerGlobal($server);
        $this->files = new FileMap($files);
        $this->requestStartTime = $this->server->get('REQUEST_TIME_FLOAT') ?? 0;
        $this->method = HttpMethod::fromString($this->server->get('REQUEST_METHOD'));
        $this->protocol = strtolower($this->server->get('SERVER_PROTOCOL'));
        $this->requestUri = parse_url($this->server->get('REQUEST_URI'), PHP_URL_PATH);

        $rawContent = file_get_contents('php://input');
        $this->rawContent = $rawContent !== false && !StringUtils::isBlank($rawContent) ? $rawContent : null;

        $this->emptyGlobals();
    }

    public function getHeaders(): HeaderMap
    {
        return $this->headers;
    }

    public function getQuery(): Map
    {
        return $this->query;
    }

    public function getPost(): Map
    {
        return $this->post;
    }

    public function getServer(): Map
    {
        return $this->server;
    }

    public function getCookies(): CookieMap
    {
        return $this->cookies;
    }

    public function getFiles(): FileMap
    {
        return $this->files;
    }

    public function getMethod(): HttpMethod
    {
        return $this->method;
    }

    public function getProtocol(): string
    {
        return $this->protocol;
    }

    public function getRequestUri(): string
    {
        return $this->requestUri;
    }

    public function getRequestStartTime(): float
    {
        return $this->requestStartTime;
    }

    public function getRawContent(): string|null
    {
        return $this->rawContent;
    }

    public function hasBody(): bool
    {
        return !$this->post->isEmpty() || !StringUtils::isBlank($this->rawContent);
    }

    public function hasFormDataBody(): bool
    {
        if (!$this->hasBody() || !$this->headers->has(HeaderMap::CONTENT_TYPE_HEADER)) {
            return false;
        }

        $contentType = strtolower($this->headers->getContentType());
        return str_starts_with($contentType, 'multipart/form-data');
    }

    public function hasXmlBody(): bool
    {
        if (!$this->hasBody() || !$this->headers->has(HeaderMap::CONTENT_TYPE_HEADER)) {
            return false;
        }

        $contentType = strtolower($this->headers->getContentType());
        return str_starts_with($contentType, 'text/xml') ||
            str_starts_with($contentType, 'application/xml') ||
            str_ends_with($contentType, '+xml');
    }

    public function hasJsonBody(): bool
    {
        if (!$this->hasBody() || !$this->headers->has(HeaderMap::CONTENT_TYPE_HEADER)) {
            return false;
        }

        $contentType = strtolower($this->headers->getContentType());
        return str_starts_with($contentType, 'application/json') ||
            str_ends_with($contentType, '+json');
    }

    public function hasFormUrlEncodedBody(): bool
    {
        if (!$this->hasBody() || !$this->headers->has(HeaderMap::CONTENT_TYPE_HEADER)) {
            return false;
        }

        $contentType = strtolower($this->headers->getContentType());
        return str_starts_with($contentType, 'application/x-www-form-urlencoded');
    }

    private function emptyGlobals(): void
    {
        $_GET = [];
        $_POST = [];
        $_SERVER = [];
        $_REQUEST = [];
        $_COOKIE = [];
        $_FILES = [];
    }
}