<?php

declare(strict_types=1);

namespace MLL\GraphQLScalars;

use GraphQL\Error\Error;
use GraphQL\Error\InvariantViolation;
use GraphQL\Language\AST\Node;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Utils\Utils;
use Spatie\Regex\Regex as RegexValidator;

abstract class Regex extends ScalarType
{
    /**
     * Return the Regex that the values are validated against.
     *
     * @return string
     */
    abstract protected function regex(): string;

    /**
     * This factory method allows you to create a Regex scalar in a one-liner.
     *
     * @param string $name
     * @param string $regex
     *
     * @return Regex
     */
    public static function make(string $name, string $regex): self
    {
        $regexClass = new class() extends Regex {
            /**
             * Return the Regex that the values are validated against.
             *
             * Must be a valid
             *
             * @return string
             */
            protected function regex(): string
            {
                return $this->regex;
            }
        };

        $regexClass->name = $name;
        $regexClass->regex = $regex;

        return $regexClass;
    }

    /**
     * Serializes an internal value to include in a response.
     *
     * @param mixed $value
     *
     * @return string
     */
    public function serialize($value): string
    {
        if (!canBeString($value)) {
            $safeValue = Utils::printSafe($value);

            throw new InvariantViolation("The given value {$safeValue} can not be serialized.");
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
     * @throws Error
     *
     * @return mixed
     */
    public function parseValue($value): string
    {
        if (!canBeString($value)) {
            $safeValue = Utils::printSafe($value);

            throw new Error("The given value {$safeValue} can not be serialized.");
        }

        $stringValue = strval($value);

        if (!$this->matchesRegex($stringValue)) {
            $safeValue = Utils::printSafeJson($stringValue);

            throw new Error("The given value {$safeValue} did not match the regex {$this->regex()}");
        }

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
     * @throws Error
     *
     * @return string
     */
    public function parseLiteral($valueNode, array $variables = null): string
    {
        if (!$valueNode instanceof StringValueNode) {
            throw new Error("Query error: Can only parse strings got: {$valueNode->kind}", [$valueNode]);
        }

        $value = $valueNode->value;

        if (!$this->matchesRegex($value)) {
            $safeValue = Utils::printSafeJson($value);

            throw new Error("The given value {$safeValue} did not match the regex {$this->regex()}", [$valueNode]);
        }

        return $value;
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    protected function matchesRegex(string $value): bool
    {
        return RegexValidator::match(
            $this->regex(),
            $value
        )->hasMatch();
    }
}
