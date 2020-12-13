<?php

declare(strict_types=1);

namespace Tests;

use GraphQL\Executor\ExecutionResult;
use GraphQL\GraphQL;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;
use MLL\GraphQLScalars\MixedScalar;
use PHPUnit\Framework\TestCase;

class MixedScalarTest extends TestCase
{
    /**
     * @var Schema
     */
    protected $schema;

    public function setUp(): void
    {
        parent::setUp();

        $mixed = new MixedScalar();

        $schemaConfig = new SchemaConfig();
        $schemaConfig->setQuery(
            new ObjectType([
                'name' => 'Query',
                'fields' => [
                    'foo' => [
                        'type' => $mixed,
                        'resolve' => function ($root, $args) {
                            return reset($args);
                        },
                        'args' => [
                            'bar' => $mixed,
                        ],
                    ],
                ],
            ])
        );

        $this->schema = new Schema($schemaConfig);
    }

    /**
     * @dataProvider singleValues
     */
    public function testSerializePassesThroughAnything($value): void
    {
        $this->assertSame(
            $value,
            (new MixedScalar())->serialize(
                $value
            )
        );
    }

    /**
     * @dataProvider singleValues
     *
     * @param mixed $value Anything
     */
    public function testParseValuePassesThroughAnything($value): void
    {
        $this->assertSame(
            $value,
            (new MixedScalar())->serialize(
                $value
            )
        );
    }

    /**
     * Provide an assortment of values that should pass the Mixed type.
     *
     * @return array<int, array<int, mixed>>
     */
    public function singleValues(): array
    {
        return [
            [null],
            [new class() {
            }],
            [[]],
            [function () {
            }],
            [[$this, 'singleValues']],
        ];
    }

    /**
     * @dataProvider literalToPhpMap
     *
     * @param mixed $expected Anything
     */
    public function testCastsValuesIntoAppropriatePhpValue(string $graphQLLiteral, string $jsonLiteral, $expected): void
    {
        $graphqlResult = $this->executeQueryWithLiteral($graphQLLiteral);
        $jsonResult = $this->executeQueryWithJsonVariable($jsonLiteral);

        $this->assertSame(
            $expected,
            $graphqlResult->data['foo']
        );

        // Ensure that values provided as JSON have the same result as GraphQL literals
        $this->assertSame(
            $graphqlResult->data,
            $jsonResult->data
        );
    }

    /**
     * Provides a GraphQL literal, a JSON literal and the expected PHP value.
     *
     * @return array<int, array<int, mixed>>
     */
    public function literalToPhpMap(): array
    {
        return [
            ['1', '1', 1],
            ['"asdf"', '"asdf"', 'asdf'],
            ['true', 'true', true],
            ['123.321', '123.321', 123.321],
            ['null', 'null', null],
            ['[1, 2]', '[1, 2]', [1, 2]],
            [
                '{a: 1}',
                '{"a": 1}',
                ['a' => 1],
            ],
            [
                '
                {
                    a: [
                        {
                            b: "c"
                        }
                    ]
                }',
                '
                {
                    "a": [
                        {
                            "b": "c"
                        }
                    ]
                }',
                [
                    'a' => [
                        [
                            'b' => 'c',
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function executeQueryWithLiteral(string $literal): ExecutionResult
    {
        $query = "
        {
            foo(bar: {$literal})
        }
        ";

        return GraphQL::executeQuery(
            $this->schema,
            $query
        );
    }

    protected function executeQueryWithJsonVariable(string $jsonLiteral): ExecutionResult
    {
        $query = '
        query Foo($var: Mixed) {
            foo(bar: $var)
        }
        ';

        $json = json_decode(
            "
        {
            \"var\": $jsonLiteral
        }
        ",
            true
        );

        return GraphQL::executeQuery(
            $this->schema,
            $query,
            null,
            null,
            $json
        );
    }
}
