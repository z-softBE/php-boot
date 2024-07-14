<?php

namespace PhpBoot\Starter\Twig;

use PhpBoot\Di\Attribute\Service;
use PhpBoot\Template\TemplateRenderer;
use Twig\Environment;

#[Service]
readonly class TwigRenderer implements TemplateRenderer
{

    public function __construct(
        private Environment $twig
    )
    {

    }

    public function render(string $templatePath, array $templateVars): string
    {
        return $this->twig->render($templatePath, $templateVars);
    }
}