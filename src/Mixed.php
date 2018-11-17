<?php

declare(strict_types=1);

namespace MLL\GraphQLScalars;

use GraphQL\Language\AST\ValueNode;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Utils\AST;

class Mixed extends ScalarType
{
    /** @var string */
    public $description = <<<'EOT'
Loose type that allows any value. Be careful when passing in large Int or Float literals,
as they may not be parsed correctly on the server side. Use String literals if you are
dealing with really large numbers to be on the safe side.
EOT;

    /**
     * Serializes an internal value to include in a response.
     *
     * @param \mixed $value
     *
     * @return \mixed
     */
    public function serialize($value)
    {
        return $value;
    }

    /**
     * Parses an externally provided value (query variable) to use as an input.
     *
     * In the case of an invalid value this method must throw an Exception
     *
     * @param \mixed $value
     *
     * @return \mixed
     */
    public function parseValue($value)
    {
        return $value;
    }

    /**
     * Parses an externally provided literal value (hardcoded in GraphQL query) to use as an input.
     *
     * In the case of an invalid node or value this method must throw an Exception
     *
     * @param ValueNode $valueNode
     * @param array|null $variables
     *
     * @throws \Exception
     *
     * @return \mixed
     */
    public function parseLiteral($valueNode, array $variables = null)
    {
        return AST::valueFromASTUntyped($valueNode);
    }
}
