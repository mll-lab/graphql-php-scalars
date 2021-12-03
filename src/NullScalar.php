<?php

declare(strict_types=1);

namespace MLL\GraphQLScalars;

use GraphQL\Error\Error;
use GraphQL\Error\InvariantViolation;
use GraphQL\Language\AST\Node;
use GraphQL\Language\AST\NullValueNode;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Utils\Utils;

class NullScalar extends ScalarType
{
    public $name = 'Null';

    public function serialize($value)
    {
        if ($value !== null) {
            throw new InvariantViolation(static::notNullMessage($value));
        }

        return null;
    }

    public function parseValue($value)
    {
        if ($value !== null) {
            throw new Error(static::notNullMessage($value));
        }

        return null;
    }

    public function parseLiteral(Node $valueNode, ?array $variables = null)
    {
        if (! $valueNode instanceof NullValueNode) {
            // Intentionally without message, as all information already in wrapped Exception
            throw new Error();
        }

        return null;
    }

    /**
     * @param mixed $value any non-null value
     */
    public static function notNullMessage($value): string
    {
        $notNull = Utils::printSafe($value);

        return "Expected null, got: {$notNull}";
    }
}
