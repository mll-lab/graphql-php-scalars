<?php

declare(strict_types=1);

namespace MLL\GraphQLScalars;

use GraphQL\Error\Error;
use GraphQL\Error\InvariantViolation;
use GraphQL\Language\AST\Node;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Utils\Utils;

abstract class StringScalar extends ScalarType
{
    /**
     * Check if the given string is a valid email.
     *
     * @param string $stringValue
     *
     * @return bool
     */
    abstract protected function isValid(string $stringValue): bool;
    
    /**
     * @param string $name The name that the scalar type will have in the schema.
     * @param string|null $description A description for the type.
     * @param callable $isValid A function that returns a boolean whether a given string is valid.
     *
     * @return StringScalar
     */
    public static function make(string $name, string $description = null, callable $isValid): StringScalar
    {
        $instance = new class() extends StringScalar {
            /**
             * Check if the given string is a valid email.
             *
             * @param string $stringValue
             *
             * @return bool
             */
            protected function isValid(string $stringValue): bool
            {
                return call_user_func($this->isValid, $stringValue);
            }
        };
    
        $instance->name = $name;
        $instance->description = $description;
        $instance->isValid = $isValid;
    
        return $instance;
    }
    
    /**
     * Serializes an internal value to include in a response.
     *
     * @param string $value
     *
     * @return string
     */
    public function serialize($value): string
    {
        $stringValue = assertString($value, InvariantViolation::class);

        if (!$this->isValid($stringValue)) {
            throw new InvariantViolation("The given string $stringValue is not a valid {$this->tryInferName()}.");
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
     * @return string
     */
    public function parseValue($value): string
    {
        $stringValue = assertString($value, Error::class);
        
        if(!$this->isValid($stringValue)) {
            $safeValue = Utils::printSafeJson($stringValue);
            throw new Error("The given string {$safeValue} is not a valid {$this->tryInferName()}.");
        }

        return $stringValue;
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
        $stringValue = assertStringLiteral($valueNode);
    
        if(!$this->isValid($stringValue)) {
            $safeValue = Utils::printSafeJson($stringValue);
            throw new Error("The given string {$safeValue} is not a valid {$this->tryInferName()}.", $valueNode);
        }

        return $stringValue;
    }
}
