<?php

declare(strict_types=1);

namespace MLL\GraphQLScalars;

use GraphQL\Error\Error;
use GraphQL\Language\AST\Node;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Language\AST\ValueNode;
use GraphQL\Utils\Utils as GraphQLUtils;

class Utils
{
    /**
     * Check if a value can be serialized to a string.
     *
     * @param mixed $value Any value
     */
    public static function canBeString($value): bool
    {
        return $value === null
            || is_scalar($value)
            || (is_object($value) && method_exists($value, '__toString'));
    }

    /**
     * Get the underlying string from a GraphQL literal and throw if Literal is not a string.
     *
     * @param Node&ValueNode $valueNode
     *
     * @throws Error
     */
    public static function extractStringFromLiteral(Node $valueNode): string
    {
        if (!$valueNode instanceof StringValueNode) {
            throw new Error(
                "Query error: Can only parse strings got: {$valueNode->kind}",
                $valueNode
            );
        }

        return $valueNode->value;
    }

    /**
     * Convert the value to a string or throw.
     *
     * @template T of \Throwable
     *
     * @param mixed $value Any value
     * @param class-string<T> $exceptionClass
     *
     * @throws T
     */
    public static function coerceToString($value, string $exceptionClass): string
    {
        if (!self::canBeString($value)) {
            $safeValue = GraphQLUtils::printSafeJson($value);

            throw new $exceptionClass(
                "The given value {$safeValue} can not be coerced to a string."
            );
        }

        return (string) $value;
    }
}
