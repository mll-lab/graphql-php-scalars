<?php declare(strict_types=1);

namespace MLL\GraphQLScalars\Tests;

use MLL\GraphQLScalars\StringScalar;

final class MyStringScalar extends StringScalar
{
    public ?string $description = 'Bar';

    protected function isValid(string $stringValue): bool
    {
        return $stringValue === 'foo';
    }
}
