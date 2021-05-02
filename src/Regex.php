<?php

declare(strict_types=1);

namespace MLL\GraphQLScalars;

use GraphQL\Error\Error;
use GraphQL\Error\InvariantViolation;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Utils\Utils as GraphQLUtils;
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
     * @return Regex
     */
    public static function make(string $name, ?string $description, string $regex): self
    {
        $concreteRegex = new class() extends Regex {
            /**
             * The regex that values are validated against.
             *
             * Is set dynamically during this class's creation.
             *
             * @var string
             */
            public static $regex;

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
     */
    abstract public static function regex(): string;

    public function serialize($value): string
    {
        $stringValue = Utils::coerceToString($value, InvariantViolation::class);

        if (!static::matchesRegex($stringValue)) {
            throw new InvariantViolation(
                static::unmatchedRegexMessage($stringValue)
            );
        }

        return $stringValue;
    }

    /**
     * Determine if the given string matches the regex defined in this class.
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

    public function parseValue($value): string
    {
        $stringValue = Utils::coerceToString($value, Error::class);

        if (!static::matchesRegex($stringValue)) {
            throw new Error(
                static::unmatchedRegexMessage($stringValue)
            );
        }

        return $value;
    }

    public function parseLiteral($valueNode, ?array $variables = null): string
    {
        $value = Utils::extractStringFromLiteral($valueNode);

        if (!static::matchesRegex($value)) {
            throw new Error(
                static::unmatchedRegexMessage($value),
                $valueNode
            );
        }

        return $value;
    }

    /**
     * Construct the error message that occurs when the given string does not match the regex.
     */
    public static function unmatchedRegexMessage(string $value): string
    {
        $safeValue = GraphQLUtils::printSafeJson($value);

        return "The given value {$safeValue} did not match the regex ".static::regex();
    }
}
