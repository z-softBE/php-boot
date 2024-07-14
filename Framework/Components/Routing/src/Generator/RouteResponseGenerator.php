<?php

namespace PhpBoot\Http\Routing\Generator;

use JMS\Serializer\Serializer;
use PhpBoot\Http\Common\HttpStatusCode;
use PhpBoot\Http\Response\Response;
use PhpBoot\Http\Routing\Attributes\Response\ResponseBody;
use PhpBoot\Http\Routing\Attributes\Response\ResponseBodyType;
use PhpBoot\Http\Routing\Attributes\Response\ResponseStatus;

readonly class RouteResponseGenerator
{
    private Serializer $serializer;

    /**
     * @param Serializer $serializer
     */
    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    public function generateResponse(
        ResponseBody $responseBody, ResponseStatus|null $responseStatus, mixed $returnValue): Response
    {
        $status = $responseStatus !== null ? $responseStatus->statusCode : HttpStatusCode::HTTP_OK;
        $content = '';
        $contentTypeHeaderValue = '';

        switch ($responseBody->type) {
            case ResponseBodyType::JSON:
                $content = $this->serializer->serialize($returnValue, 'json');
                $contentTypeHeaderValue = $responseBody->produces ?? 'application/json';
                break;
            case ResponseBodyType::XML:
                $content = $this->serializer->serialize($returnValue, 'xml');
                $contentTypeHeaderValue = $responseBody->produces ?? 'text/xml';
                break;
            case ResponseBodyType::RAW:
                $content = (string)$returnValue;
                $contentTypeHeaderValue = $responseBody->produces ?? 'text/plain';
                break;
        }

        return new Response($content, $status, ['Content-Type' => $contentTypeHeaderValue]);
    }

}