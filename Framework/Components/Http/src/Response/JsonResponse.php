<?php

namespace PhpBoot\Http\Response;

use PhpBoot\Http\Common\HeaderMap;
use PhpBoot\Http\Common\HttpProtocolVersion;
use PhpBoot\Http\Common\HttpStatusCode;
use PhpBoot\Http\Request\Request;

class JsonResponse extends Response
{
    protected array|object|string $data;
    protected int $encodingOptions;

    public function __construct(
        string|object|array $data = [],
        HttpStatusCode $statusCode = HttpStatusCode::HTTP_OK,
        array $headers = [],
        array $cookies = [],
        HttpProtocolVersion $protocolVersion = HttpProtocolVersion::HTTP_1_1
    )
    {
        parent::__construct('', $statusCode, $headers, $cookies, $protocolVersion);

        $this->encodingOptions = 0;
        $this->setData($data);
    }

    public function setData(object|array|string $data): void
    {
        $this->data = $data;

        if (is_string($data)) {
            $this->setContent($data);
            return;
        }

        $this->setData(json_encode($data, $this->encodingOptions));
    }

    public function setEncodingOptions(int $encodingOptions): void
    {
        $this->encodingOptions = $encodingOptions;
    }

    #[\Override]
    public function prepare(Request $request): void
    {
        parent::prepare($request);

        if (!$this->headers->has(HeaderMap::CONTENT_TYPE_HEADER) ||
            !str_starts_with($this->headers->getContentType(), 'application/json') ||
            !str_ends_with($this->headers->getContentType(), '+json')
        ) {
            $this->headers->add(HeaderMap::CONTENT_TYPE_HEADER, 'application/json');
        }
    }


}