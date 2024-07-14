<?php

namespace PhpBoot\Http\Routing\Attributes\Response;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
readonly class ResponseBody
{
    public function __construct(
        public ResponseBodyType $type,
        public string|null $produces = null
    )
    {

    }
}