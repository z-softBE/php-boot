<?php

namespace PhpBoot\Exception;

use Exception;
use PhpBoot\Http\Response\Response;

class ExceptionHandlerDelegator
{
    /** @var ExceptionHandler[] */
    private array $handlers = [];

    public function registerHandler(ExceptionHandler $handler): void
    {
        $this->handlers[] = $handler;
    }

    public function handleException(Exception $exception): Response
    {
        $handler = $this->findHandler($exception);

        if ($handler !== null) {
            return $handler->handleException($exception);
        }

        throw $exception;
    }

    private function findHandler(Exception $exception): ExceptionHandler|null
    {
        $selectedHandler = null;
        foreach ($this->handlers as $handler) {
            if (
                $handler->supports($exception) &&
                ($selectedHandler === null || $selectedHandler->priority() < $handler->priority())
            ) {
                $selectedHandler = $handler;
            }
        }

        return $selectedHandler;
    }
}