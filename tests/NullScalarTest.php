<?php declare(strict_types=1);

namespace MLL\GraphQLScalars\Tests;

use GraphQL\Executor\ExecutionResult;
use GraphQL\GraphQL;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;
use MLL\GraphQLScalars\NullScalar;
use PHPUnit\Framework\TestCase;

final class NullScalarTest extends TestCase
{
    private Schema $schema;

    /**
     * @var mixed will be returned by field mixed
     */
    private $return;

    public function setUp(): void
    {
        parent::setUp();

        $null = new NullScalar();

        $schemaConfig = new SchemaConfig();
        $schemaConfig->setQuery(
            new ObjectType([
                'name' => 'Query',
                'fields' => [
                    'foo' => [
                        'type' => $null,
                        'resolve' => function ($root, array $args) {
                            return reset($args);
                        },
                        'args' => [
                            'bar' => $null,
                        ],
                    ],
                    'mixed' => [
                        'type' => $null,
                        'resolve' => function () {
                            return $this->return;
                        },
                    ],
                ],
            ])
        );

        $this->schema = new Schema($schemaConfig);
    }

    public function testAllowsNullArguments(): void
    {
        $graphqlResult = $this->executeQueryWithLiteral('null');
        self::assertNull($graphqlResult->data['foo']);

        $jsonResult = $this->executeQueryWithJsonVariable('null');
        self::assertNull($jsonResult->data['foo']);
    }

    public function testForbidsNonNullArguments(): void
    {
        $graphqlResult = $this->executeQueryWithLiteral('1');
        self::assertNull($graphqlResult->data);
        self::assertSame('Field "foo" argument "bar" requires type Null, found 1.', $graphqlResult->errors[0]->getMessage());

        $jsonResult = $this->executeQueryWithJsonVariable('1');
        self::assertNull($jsonResult->data);
        self::assertSame('Variable "$var" got invalid value 1; Expected type Null; Expected null, got: 1.', $jsonResult->errors[0]->getMessage());
    }

    public function testForbidsNonNullReturn(): void
    {
        $this->return = 1;
        $graphqlResult = GraphQL::executeQuery($this->schema, /** @lang GraphQL */ '{ mixed }');
        self::assertSame('Expected a value of type "Null" but received: 1', $graphqlResult->errors[0]->getMessage());
        self::assertSame(['mixed' => null], $graphqlResult->data);
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
        query Foo($var: Null) {
            foo(bar: $var)
        }
        ';

        /** @var array{var: mixed} $json */
        $json = \Safe\json_decode(/** @lang JSON */ <<<JSON
            {
                "var": ${jsonLiteral}
            }
            JSON,
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
