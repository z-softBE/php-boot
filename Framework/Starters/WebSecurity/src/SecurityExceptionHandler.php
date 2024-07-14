<?php

namespace PhpBoot\Starter\Web\Security;

use Exception;
use PhpBoot\Di\Attribute\Service;
use PhpBoot\Exception\ExceptionHandler;
use PhpBoot\Exception\ExceptionHandlerDelegator;
use PhpBoot\Http\Common\HttpStatusCode;
use PhpBoot\Http\Common\HttpStatusCodeMessages;
use PhpBoot\Http\Response\Response;
use PhpBoot\Security\Exception\ForbiddenException;
use PhpBoot\Security\Exception\UnauthorizedException;

#[Service]
class SecurityExceptionHandler implements ExceptionHandler
{
    public function __construct(ExceptionHandlerDelegator $exceptionHandlerDelegator)
    {
        $exceptionHandlerDelegator->registerHandler($this);
    }

    public function handleException(Exception $exception): Response
    {
        $httpStatusCode = match (true) {
            $exception instanceof UnauthorizedException => HttpStatusCode::HTTP_UNAUTHORIZED,
            $exception instanceof ForbiddenException => HttpStatusCode::HTTP_FORBIDDEN
        };

        return new Response(
            HttpStatusCodeMessages::STATUS_CODE_TO_MESSAGE[$httpStatusCode->value],
            $httpStatusCode
        );
    }

    public function supports(Exception $exception): bool
    {
        return $exception instanceof ForbiddenException || $exception instanceof UnauthorizedException;
    }

    public function priority(): int
    {
        return 0;
    }


}