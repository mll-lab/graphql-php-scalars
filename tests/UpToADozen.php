<?php declare(strict_types=1);

namespace MLL\GraphQLScalars\Tests;

use MLL\GraphQLScalars\IntRange;

final class UpToADozen extends IntRange
{
    protected function min(): int
    {
        return 1;
    }

    protected function max(): int
    {
        return 12;
    }
}
