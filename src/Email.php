<?php

declare(strict_types=1);

namespace MLL\GraphQLScalars;

use GraphQL\Error\InvariantViolation;
use GraphQL\Language\AST\Node;
use GraphQL\Type\Definition\ScalarType;

class Email extends ScalarType
{
    /**
     * Serializes an internal value to include in a response.
     *
     * @param string $value
     *
     * @return string
     */
    public function serialize($value): string
    {
        if (!canBeString($value)) {
            $valueClass = get_class($value);

            throw new InvariantViolation("The given value of class $valueClass can not be serialized.");
        }

        $stringValue = strval($value);

        if (!$this->matchesRegex($stringValue)) {
            throw new InvariantViolation("The given string $stringValue did not match the regex {$this->regex()}");
        }

        return $stringValue;
    }

    /**
     * Parses an externally provided value (query variable) to use as an input.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function parseValue($value)
    {
        // TODO implement validation

        return $value;
    }

    /**
     * Parses an externally provided literal value (hardcoded in GraphQL query) to use as an input.
     *
     * E.g.
     * {
     *   user(email: "user@example.com")
     * }
     *
     * @param Node $valueNode
     * @param array $variables
     *
     * @return mixed
     */
    public function parseLiteral($valueNode, array $variables = null)
    {
        // TODO implement validation

        return $valueNode->value;
    }
}
