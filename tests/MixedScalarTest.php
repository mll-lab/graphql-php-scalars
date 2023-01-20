<?php declare(strict_types=1);

namespace MLL\GraphQLScalars\Tests;

use GraphQL\Executor\ExecutionResult;
use GraphQL\GraphQL;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;
use MLL\GraphQLScalars\MixedScalar;
use PHPUnit\Framework\TestCase;

final class MixedScalarTest extends TestCase
{
    private Schema $schema;

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
                        'resolve' => function ($root, array $args) {
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
     * @param mixed $value Anything
     */
    public function testSerializePassesThroughAnything($value): void
    {
        self::assertSame(
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
        self::assertSame(
            $value,
            (new MixedScalar())->serialize(
                $value
            )
        );
    }

    /**
     * Provide an assortment of values that should pass the Mixed type.
     *
     * @return array<int, array{0: mixed}>
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

        self::assertSame(
            $expected,
            $graphqlResult->data['foo']
        );

        // Ensure that values provided as JSON have the same result as GraphQL literals
        self::assertSame(
            $graphqlResult->data,
            $jsonResult->data
        );
    }

    /**
     * Provides a GraphQL literal, a JSON literal and the expected PHP value.
     *
     * @return array<int, array{0: string, 1: string, 2: mixed}>
     */
    public function literalToPhpMap(): array
    {
        return [
            [/** @lang GraphQL */ '1', /** @lang JSON */ '1', 1],
            [/** @lang GraphQL */ '"asdf"', /** @lang JSON */ '"asdf"', 'asdf'],
            [/** @lang GraphQL */ 'true', /** @lang JSON */ 'true', true],
            [/** @lang GraphQL */ '123.321', /** @lang JSON */ '123.321', 123.321],
            [/** @lang GraphQL */ 'null', /** @lang JSON */ 'null', null],
            [/** @lang GraphQL */ '[1, 2]', /** @lang JSON */ '[1, 2]', [1, 2]],
            [
/** @lang GraphQL */ '{a: 1}',
/** @lang JSON */ '{"a": 1}',
                ['a' => 1],
            ],
            [
/** @lang GraphQL */ '
                {
                    a: [
                        {
                            b: "c"
                        }
                    ]
                }',
/** @lang JSON */ '
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
        $query = /** @lang GraphQL */ "
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
        $query = /** @lang GraphQL */ '
        query Foo($var: Mixed) {
            foo(bar: $var)
        }
        ';

        /** @var array<string, mixed> $json */
        $json = \Safe\json_decode(/** @lang JSON */ <<<JSON
                    {
                        "var": ${jsonLiteral}
                    }
            JSON
            ,
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
