<?php

declare(strict_types=1);

namespace Tests;

use MLL\GraphQLScalars\StringScalar;

class MyStringScalar extends StringScalar
{
    /**
     * @var string
     */
    public $description = 'Bar';

    /**
     * Check if the given string is exactly "foo".
     *
     * @param string $stringValue
     *
     * @return bool
     */
    protected function isValid(string $stringValue): bool
    {
        return $stringValue === 'foo';
    }
}
