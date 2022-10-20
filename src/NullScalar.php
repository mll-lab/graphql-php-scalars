<?php declare(strict_types=1);

namespace MLL\GraphQLScalars;

use GraphQL\Error\Error;
use GraphQL\Error\InvariantViolation;
use GraphQL\Language\AST\NullValueNode;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Utils\Utils;

class NullScalar extends ScalarType
{
    public $name = 'Null';

    public $description /** @lang Markdown */
        = 'Always `null`. Strictly validates value is non-null, no coercion.';

    public function serialize($value)
    {
        if (null !== $value) {
            throw new InvariantViolation(static::notNullMessage($value));
        }

        return null;
    }

    public function parseValue($value)
    {
        if (null !== $value) {
            throw new Error(static::notNullMessage($value));
        }

        return null;
    }

    public function parseLiteral($valueNode, ?array $variables = null)
    {
        if (! $valueNode instanceof NullValueNode) {
            // Intentionally without message, as all information is already present in the wrapped error
            throw new Error('');
        }

        return null;
    }

    /**
     * @param mixed $value any non-null value
     */
    public static function notNullMessage($value): string
    {
        $notNull = Utils::printSafeJson($value);

        return "Expected null, got: {$notNull}.";
    }
}
