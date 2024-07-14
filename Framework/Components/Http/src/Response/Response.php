<?php

namespace PhpBoot\Http\Response;

use PhpBoot\Http\Common\HeaderMap;
use PhpBoot\Http\Common\HttpMethod;
use PhpBoot\Http\Common\HttpProtocolVersion;
use PhpBoot\Http\Common\HttpStatusCode;
use PhpBoot\Http\Common\HttpStatusCodeMessages;
use PhpBoot\Http\Cookie\Cookie;
use PhpBoot\Http\Cookie\CookieMap;
use PhpBoot\Http\Request\Request;

class Response
{
    protected HeaderMap $headers;
    protected CookieMap $cookies;
    protected string $content;
    protected HttpStatusCode $statusCode;
    protected HttpProtocolVersion $protocolVersion;

    public function __construct(
        string $content = '',
        HttpStatusCode $statusCode = HttpStatusCode::HTTP_OK,
        array $headers = [],
        array $cookies = [],
        HttpProtocolVersion $protocolVersion = HttpProtocolVersion::HTTP_1_1
    )
    {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->headers = HeaderMap::createFromArray($headers);
        $this->cookies = new CookieMap($cookies);
        $this->protocolVersion = $protocolVersion;
    }

    public function getHeaders(): HeaderMap
    {
        return $this->headers;
    }

    public function setHeaders(HeaderMap $headers): void
    {
        $this->headers = $headers;
    }

    public function getCookies(): CookieMap
    {
        return $this->cookies;
    }

    public function setCookies(CookieMap $cookies): void
    {
        $this->cookies = $cookies;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getStatusCode(): HttpStatusCode
    {
        return $this->statusCode;
    }

    public function setStatusCode(HttpStatusCode $statusCode): void
    {
        $this->statusCode = $statusCode;
    }

    public function getProtocolVersion(): HttpProtocolVersion
    {
        return $this->protocolVersion;
    }

    public function setProtocolVersion(HttpProtocolVersion $protocolVersion): void
    {
        $this->protocolVersion = $protocolVersion;
    }

    public function isInformational(): bool
    {
        return $this->statusCode >= 100 && $this->statusCode < 200;
    }

    public function isSuccessful(): bool
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }

    public function isRedirection(): bool
    {
        return $this->statusCode >= 300 && $this->statusCode < 400;
    }

    public function isClientError(): bool
    {
        return $this->statusCode >= 400 && $this->statusCode < 500;
    }

    public function isServerError(): bool
    {
        return $this->statusCode >= 500 && $this->statusCode < 600;
    }

    public function isEmpty(): bool
    {
        return $this->isInformational() ||
            $this->statusCode === HttpStatusCode::HTTP_NO_CONTENT ||
            $this->statusCode === HttpStatusCode::HTTP_NOT_MODIFIED;
    }

    public function setCookie(Cookie $cookie): void
    {
        $this->cookies->add($cookie);
    }

    public function setHeader(string $name, string $value): void
    {
        $this->headers->add($name, $value);
    }

    public function setContentType(string $contentType): void
    {
        $this->headers->add(HeaderMap::CONTENT_TYPE_HEADER, $contentType);
    }

    /**
     * Prepares response based on the request
     *
     * @param Request $request
     * @return void
     */
    public function prepare(Request $request): void
    {
        if ($this->isEmpty()) {
            $this->content = '';
            $this->headers->remove(HeaderMap::CONTENT_TYPE_HEADER);
            $this->headers->remove(HeaderMap::CONTENT_LENGTH_HEADER);
            ini_set('default_mimetype', ''); // prevents PHP from setting the Content-Type header automatically
            return;
        }

        if ($this->headers->has('Transfer-Encoding')) {
            $this->headers->remove(HeaderMap::CONTENT_LENGTH_HEADER);
        }

        if ($request->getMethod() === HttpMethod::HEAD) {
            $length = $this->headers->getContentLength() ?? strlen($this->content);
            $this->headers->add(HeaderMap::CONTENT_LENGTH_HEADER, $length);
        }
    }

    public function send(): void
    {
        $this->sendCookies();
        $this->sendHeaders();
        $this->sendContent();
    }

    protected function sendCookies(): void
    {
        /** @var Cookie $cookie */
        foreach ($this->cookies as $cookie) {
            setcookie(
              $cookie->getName(),
              $cookie->getValue(),
              $cookie->getExpires() ?? 0,
              $cookie->getPath() ?? '',
              $cookie->getDomain() ?? '',
              $cookie->isSecure(),
              $cookie->isHttpOnly()
            );
        }
    }

    protected function sendHeaders(): void
    {
        if (headers_sent()) {
            return;
        }

        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}", true, $this->statusCode->value);
        }

        $statusText = HttpStatusCodeMessages::STATUS_CODE_TO_MESSAGE[$this->statusCode->value];
        header("HTTP/{$this->protocolVersion->value} {$this->statusCode->value} {$statusText}", true, $this->statusCode->value);
    }

    protected function sendContent(): void
    {
        echo $this->content;
    }
}