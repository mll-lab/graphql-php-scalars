<?php

declare(strict_types=1);

namespace Tests;

use MLL\GraphQLScalars\StringScalar;

class MyStringScalar extends StringScalar
{
    public $description = 'Bar';

    protected function isValid(string $stringValue): bool
    {
        return $stringValue === 'foo';
    }
}
