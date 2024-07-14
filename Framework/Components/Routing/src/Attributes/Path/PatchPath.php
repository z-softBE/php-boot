<?php

namespace PhpBoot\Http\Routing\Attributes\Path;

use Attribute;
use PhpBoot\Http\Common\HttpMethod;

#[Attribute(Attribute::TARGET_METHOD)]
readonly class PatchPath extends Path
{
    public function __construct(string $path)
    {
        parent::__construct($path, HttpMethod::PATCH);
    }
}