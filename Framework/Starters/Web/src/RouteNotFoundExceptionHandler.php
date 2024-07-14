<?php

namespace PhpBoot\Starter\Web;

use Exception;
use PhpBoot\Di\Attribute\Service;
use PhpBoot\Exception\ExceptionHandler;
use PhpBoot\Exception\ExceptionHandlerDelegator;
use PhpBoot\Http\Common\HttpStatusCode;
use PhpBoot\Http\Common\HttpStatusCodeMessages;
use PhpBoot\Http\Response\Response;
use PhpBoot\Http\Routing\Exception\RouteNotFoundException;

#[Service]
class RouteNotFoundExceptionHandler implements ExceptionHandler
{
    public function __construct(ExceptionHandlerDelegator $exceptionHandlerDelegator)
    {
        $exceptionHandlerDelegator->registerHandler($this);
    }

    public function handleException(Exception $exception): Response
    {
        $statusCode = HttpStatusCode::HTTP_NOT_FOUND;

        return new Response(
            HttpStatusCodeMessages::STATUS_CODE_TO_MESSAGE[$statusCode->value],
            $statusCode
        );
    }

    public function supports(Exception $exception): bool
    {
        return $exception instanceof RouteNotFoundException;
    }

    public function priority(): int
    {
        return 0;
    }


}