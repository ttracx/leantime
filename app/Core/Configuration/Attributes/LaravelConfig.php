<?php

namespace Safe4Work\Core\Configuration\Attributes;

use Attribute;

#[Attribute]
class LaravelConfig
{
    public function __construct(
        public string $config,
    ) {
        //
    }
}
