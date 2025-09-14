<?php

namespace Safe4Work\Core\Db;

use Attribute;

#[Attribute]
class DbColumn
{
    public function __construct(
        public string $name,
    ) {
        //
    }
}
