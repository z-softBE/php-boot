<?php

namespace PhpBoot\Starter\Twig\Config;

use PhpBoot\Di\Attribute\Bean;
use PhpBoot\Di\Attribute\Configuration;
use PhpBoot\Di\Attribute\Property;
use Twig\Environment;
use Twig\Extension\ExtensionInterface;
use Twig\Loader\FilesystemLoader;

#[Configuration]
class TwigConfig
{
    public function __construct(
        #[Property('phpBoot.rootDirectory')] private string $rootDirectory,
        #[Property('phpBoot.cacheDirectory')] private string $cacheDirectory,
        #[Property('twig.extension')] private array $extensionClassNames = [],
    )
    {

    }

    #[Bean]
    public function twigEnvironment(): Environment
    {
        $loader = new FilesystemLoader('templates', $this->rootDirectory);
        $twig = new Environment(
            $loader,
            ['cache' => $this->cacheDirectory . DIRECTORY_SEPARATOR . 'Twig']
        );

        foreach ($this->extensionClassNames  as $className) {
            if (!is_subclass_of($className, ExtensionInterface::class)) continue;

            $twig->addExtension(new $className());
        }

        return $twig;
    }
}