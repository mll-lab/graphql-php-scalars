<?php

declare(strict_types=1);

namespace MLL\GraphQLScalars;

/**
 * Check if a value can be serialized to a string.
 *
 * @param $value
 *
 * @return bool
 */
function canBeString($value)           : bool
{
    return $value === null
        || is_scalar($value)
        || (is_object($value) && method_exists($value, '__toString'));
}
