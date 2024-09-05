<?php declare(strict_types=1);

namespace MLL\GraphQLScalars\Tests;

use MLL\GraphQLScalars\IntRange;

final class UpToADozen extends IntRange
{
    protected static function min(): int
    {
        return 1;
    }

    protected static function max(): int
    {
        return 12;
    }
}
