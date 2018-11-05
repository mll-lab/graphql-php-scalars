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
     * @return Regex
     */
    public static function make(string $name, string $description = null, string $regex): self
    {
        $regexClass = new class() extends Regex {
            /** @var string Is set dynamically during this class creation. */
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

        $regexClass->name = $name;
        $regexClass->description = $description;
        $regexClass::$regex = $regex;

        return $regexClass;
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
        $stringValue = assertString($value, InvariantViolation::class);

        if (!$this->matchesRegex($stringValue)) {
            throw new InvariantViolation(
                $this->unmatchedRegexMessage($stringValue)
            );
        }

        return $stringValue;
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    protected function matchesRegex(string $value): bool
    {
        return RegexValidator::match(
            static::regex(),
            $value
        )->hasMatch();
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
        $stringValue = assertString($value, Error::class);

        if (!$this->matchesRegex($stringValue)) {
            throw new Error(
                $this->unmatchedRegexMessage($stringValue)
            );
        }

        return $value;
    }

    /**
     * Parses an externally provided literal value (hardcoded in GraphQL query) to use as an input.
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
        $value = assertStringLiteral($valueNode);

        if (!$this->matchesRegex($value)) {
            throw new Error(
                $this->unmatchedRegexMessage($value),
                [$valueNode]
            );
        }

        return $value;
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public function unmatchedRegexMessage(string $value): string
    {
        $safeValue = Utils::printSafeJson($value);

        return "The given value {$safeValue} did not match the regex " . static::regex();
    }
}
