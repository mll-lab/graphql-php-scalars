<?php

declare(strict_types=1);

namespace MLL\GraphQLScalars;

use GraphQL\Error\Error;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Utils\Utils;

/**
 * Check if a value can be serialized to a string.
 *
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

/**
 * @param $valueNode
 *
 * @return string
 * @throws Error
 */
function assertStringLiteral($valueNode): string
{
    if (!$valueNode instanceof StringValueNode) {
        throw new Error("Query error: Can only parse strings got: {$valueNode->kind}", [$valueNode]);
    }
    
    return $valueNode->value;
}

/**
 * Ensure the value is a string and throw an exception if not.
 *
 * @param $value
 * @param string $exceptionClass
 *
 * @return string
 */
function assertString($value, string $exceptionClass): string
{
    if (!canBeString($value)) {
        $safeValue = Utils::printSafe($value);
        
        throw new $exceptionClass("The given value {$safeValue} can not be serialized.");
    }
    
    return strval($value);
}
