<?php

namespace PhpBoot\Exception;

use Exception;
use PhpBoot\Http\Response\Response;

interface ExceptionHandler
{
    public function handleException(Exception $exception): Response;

    public function supports(Exception $exception): bool;

    /**
     * The priority of  this handler
     * The highest priority get executed of multiple handlers are found
     *
     * @return int
     */
    public function priority(): int;
}