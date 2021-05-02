<?php

declare(strict_types=1);

namespace MLL\GraphQLScalars;

use GraphQL\Error\Error;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Utils\Utils as GraphQLUtils;
use Safe\Exceptions\JsonException;

class JSON extends ScalarType
{
    public $description = /** @lang Markdown */
        'Arbitrary data encoded in JavaScript Object Notation. See https://www.json.org/.';

    public function serialize($value): string
    {
        return \Safe\json_encode($value);
    }

    public function parseValue($value)
    {
        return $this->decodeJSON($value);
    }

    public function parseLiteral($valueNode, ?array $variables = null)
    {
        if (!property_exists($valueNode, 'value')) {
            throw new Error(
                'Can only parse literals that contain a value, got '.GraphQLUtils::printSafeJson($valueNode)
            );
        }

        return $this->decodeJSON($valueNode->value);
    }

    /**
     * Try to decode a user-given JSON value.
     *
     * @param mixed $value A user given JSON
     *
     * @throws Error
     *
     * @return mixed The decoded value
     */
    protected function decodeJSON($value)
    {
        try {
            $decoded = \Safe\json_decode($value);
        } catch (JsonException $jsonException) {
            throw new Error(
                $jsonException->getMessage()
            );
        }

        return $decoded;
    }
}
