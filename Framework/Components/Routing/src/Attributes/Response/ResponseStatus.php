<?php

namespace PhpBoot\Http\Routing\Attributes\Response;

use Attribute;
use PhpBoot\Http\Common\HttpStatusCode;

#[Attribute(Attribute::TARGET_METHOD)]
readonly class ResponseStatus
{
    public function __construct(
        public HttpStatusCode $statusCode
    )
    {

    }

}