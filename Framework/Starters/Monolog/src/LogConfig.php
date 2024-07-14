<?php

namespace PhpBoot\Starter\Monolog;

use Monolog\Logger;
use PhpBoot\Di\Attribute\Bean;
use PhpBoot\Di\Attribute\Configuration;
use PhpBoot\Di\Attribute\Property;

#[Configuration]
class LogConfig
{
    public function __construct(
        #[Property('phpBoot.rootDirectory')] private string $rootDirectory,
        #[Property('logging.name')] private string $channelName,
        #[Property('logging.fileHandler')] private array|null $fileHandler = null,
        #[Property('logging.rotatingFileHandler')] private array|null $rotatingFileHandler = null,
        #[Property('logging.syslogHandler')] private array|null $syslogHandler = null,
    )
    {

    }

    #[Bean]
    public function logger(): Logger
    {
        $handlers = [];

        if ($this->fileHandler !== null) {
            $handlers[] = PushHandlerFactory::createFileHandler($this->fileHandler, $this->rootDirectory);
        }

        if ($this->rotatingFileHandler !== null) {
            $handlers[] = PushHandlerFactory::createRotatingFileHandler($this->rotatingFileHandler, $this->rootDirectory);
        }

        if ($this->syslogHandler !== null) {
            $handlers[] = PushHandlerFactory::createSyslogHandler($this->syslogHandler);
        }

        return new Logger($this->channelName, $handlers);
    }
}