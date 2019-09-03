<?php

declare(strict_types=1);

namespace MLL\GraphQLScalars;

use GraphQL\Error\Error;
use GraphQL\Error\InvariantViolation;
use GraphQL\Language\AST\Node;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Utils\Utils;
use Spatie\Regex\Regex as RegexValidator;

abstract class Regex extends ScalarType
{
    /**
     * This factory method allows you to create a Regex scalar in a one-liner.
     *
     * @param string $name The name that the scalar type will have in the schema.
     * @param string|null $description A description for the type.
     * @param string $regex The regular expression that is validated against.
     *
     * @return static
     */
    public static function make(string $name, ?string $description, string $regex): self
    {
        $concreteRegex = new class() extends Regex {
            /**
             * The regex that values are validated against.
             *
             * Is set dynamically during this class creation.
             *
             * @var string
             */
            public static $regex;

            /**
             * Return the Regex that the values are validated against.
             *
             * Must be a valid
             *
             * @return string
             */
            public static function regex(): string
            {
                return static::$regex;
            }
        };

        $concreteRegex->name = $name;
        $concreteRegex->description = $description;
        $concreteRegex::$regex = $regex;

        return $concreteRegex;
    }

    /**
     * Return the Regex that the values are validated against.
     *
     * @return string
     */
    abstract public static function regex(): string;

    /**
     * Serializes an internal value to include in a response.
     *
     * @param mixed $value
     *
     * @return string
     */
    public function serialize($value): string
    {
        $stringValue = coerceToString($value, InvariantViolation::class);

        if (!static::matchesRegex($stringValue)) {
            throw new InvariantViolation(
                static::unmatchedRegexMessage($stringValue)
            );
        }

        return $stringValue;
    }

    /**
     * Determine if the given string matches the regex defined in this class.
     *
     * @param string $value
     *
     * @return bool
     */
    protected static function matchesRegex(string $value): bool
    {
        return RegexValidator
            ::match(
                static::regex(),
                $value
            )
            ->hasMatch();
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
        $stringValue = coerceToString($value, Error::class);

        if (!static::matchesRegex($stringValue)) {
            throw new Error(
                static::unmatchedRegexMessage($stringValue)
            );
        }

        return $value;
    }

    /**
     * Parses an externally provided literal value (hardcoded in GraphQL query) to use as an input.
     *
     * @param Node $valueNode
     * @param mixed[]|null $variables
     *
     * @throws Error
     *
     * @return string
     */
    public function parseLiteral($valueNode, ?array $variables = null): string
    {
        $value = extractStringFromLiteral($valueNode);

        if (!static::matchesRegex($value)) {
            throw new Error(
                static::unmatchedRegexMessage($value),
                [$valueNode]
            );
        }

        return $value;
    }

    /**
     * Construct the error message that occurs when the given string does not match the regex.
     *
     * @param string $value
     *
     * @return string
     */
    public static function unmatchedRegexMessage(string $value): string
    {
        $safeValue = Utils::printSafeJson($value);

        return "The given value {$safeValue} did not match the regex ".static::regex();
    }
}
