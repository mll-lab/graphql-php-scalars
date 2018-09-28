<?php

namespace MLL\GraphQLScalars;

/**
 * @param $value
 *
 * @return bool
 */
function canBeString($value): bool
{
    return $value === null
        || is_scalar($value)
        || (is_object($value) && method_exists($value, '__toString'));
}
