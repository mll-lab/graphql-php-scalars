<?php

declare(strict_types=1);

namespace Tests;

use GraphQL\Executor\ExecutionResult;
use GraphQL\GraphQL;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;
use MLL\GraphQLScalars\Mixed;

class MixedTest extends \PHPUnit\Framework\TestCase
{
    /** @var Schema */
    protected $schema;

    public function setUp()
    {
        parent::setUp();

        $mixed = new Mixed();

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
     *
     * @param $value
     */
    public function testSerializePassesThroughAnything($value)
    {
        $this->assertSame(
            $value,
            (new Mixed())->serialize(
                $value
            )
        );
    }

    /**
     * @dataProvider singleValues
     *
     * @param $value
     */
    public function testParseValuePassesThroughAnything($value)
    {
        $this->assertSame(
            $value,
            (new Mixed())->serialize(
                $value
            )
        );
    }

    public function singleValues()
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
     * @param string $graphQLLiteral
     * @param string $jsonLiteral
     * @param $expected
     */
    public function testCastsValuesIntoAppropriatePhpValue(string $graphQLLiteral, string $jsonLiteral, $expected)
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
     * Provides a GraphQL literal, a Json literal and the expected PHP value.
     *
     * @return array
     */
    public function literalToPhpMap()
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

    /**
     * @param string $literal
     *
     * @return ExecutionResult
     */
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

    /**
     * @param string $jsonLiteral
     *
     * @return ExecutionResult
     */
    protected function executeQueryWithJsonVariable(string $jsonLiteral): ExecutionResult
    {
        $query = '
        query Foo($var: Mixed) {
            foo(bar: $var)
        }
        ';

        $json = json_decode("
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
