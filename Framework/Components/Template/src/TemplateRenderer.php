<?php

namespace PhpBoot\Template;

interface TemplateRenderer
{
    public function render(string $templatePath, array $templateVars): string;
}