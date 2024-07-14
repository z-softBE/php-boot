<?php

namespace PhpBoot\Starter\Monolog;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogHandler;
use Monolog\Level;
use PhpBoot\Starter\Monolog\Exception\LoggingException;

final readonly class PushHandlerFactory
{
    private function __construct()
    {

    }

    public static function createFileHandler(array $options, string $rootDirectory): StreamHandler
    {
        $resource = $options['path'] ?? 'php://stdout';
        $level = isset($options['level']) ? self::convertLevel($options['level']) : Level::Info;

        if (!str_starts_with($resource, 'php://'))  {
            $resource = $rootDirectory . DIRECTORY_SEPARATOR . $resource;
        }

        return new StreamHandler($resource, $level);
    }

    public static function createRotatingFileHandler(array $options, string $rootDirectory): RotatingFileHandler
    {
        if (!isset($options['path'])) {
            throw new LoggingException("The rotating file handler needs to have a path property");
        }

        $resource = $rootDirectory . DIRECTORY_SEPARATOR . $options['path'];
        $maxFiles = $options['maxFiles'] ?? 0;
        $level = isset($options['level']) ? self::convertLevel($options['level']) : Level::Info;

        return new RotatingFileHandler($resource, $maxFiles, $level);
    }

    private static function convertLevel(string $level): Level
    {
        return match (strtoupper($level)) {
            "EMERGENCY" => Level::Emergency,
            "ALERT" => Level::Alert,
            "CRITICAL" => Level::Critical,
            "ERROR" => Level::Error,
            "WARNING" => Level::Warning,
            "NOTICE" => Level::Notice,
            "INFO" => Level::Info,
            "DEBUG" => Level::Debug,
            default => new LoggingException("Could not process logging level: {$level}")
        };
    }

    public static function createSyslogHandler(array $options): SyslogHandler
    {
        $ident = $options['ident'] ?? '';
        $facility = $options['facility'] ?? LOG_USER;
        $level = isset($options['level']) ? self::convertLevel($options['level']) : Level::Info;

        return new SyslogHandler($ident, $facility, $level);
    }
}