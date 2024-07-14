<?php

namespace PhpBoot\Starter\Twig;

use PhpBoot\Di\Attribute\Service;
use PhpBoot\Event\EventSystem;
use PhpBoot\Event\Model\Event;
use PhpBoot\Template\TemplateRenderer;
use Twig\Environment;

#[Service]
readonly class TwigRenderer implements TemplateRenderer
{

    public function __construct(
        private Environment $twig,
        private EventSystem $eventSystem
    )
    {

    }

    public function render(string $templatePath, array $templateVars): string
    {
        $this->eventSystem->sendEvent(new Event('twigRenderer.render', ['path' => $templatePath, 'var' => $templateVars]));

        return $this->twig->render($templatePath, $templateVars);
    }
}