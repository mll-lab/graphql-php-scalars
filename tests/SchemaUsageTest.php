<?php declare(strict_types=1);

namespace MLL\GraphQLScalars\Tests;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;
use GraphQL\Utils\SchemaPrinter;
use MLL\GraphQLScalars\Date;
use MLL\GraphQLScalars\Email;
use PHPUnit\Framework\TestCase;

final class SchemaUsageTest extends TestCase
{
    public function testUseScalarInSchema(): void
    {
        // Make sure to instantiate the class only once
        // See http://webonyx.github.io/graphql-php/type-system/#type-registry
        $email = new Email();

        $schemaConfig = new SchemaConfig();
        $schemaConfig->setQuery(
            new ObjectType([
                'name' => 'Query',
                'fields' => [
                    'foo' => $email,
                    'bar' => $email,
                ],
            ])
        );

        $schema = new Schema($schemaConfig);

        self::assertEmpty($schema->validate());
    }

    public function testScalarsSpecifyReadmeUrl(): void
    {
        self::requireSpecifiedByURLSupport();

        $date = new Date();

        self::assertSame('https://github.com/mll-lab/graphql-php-scalars#date', $date->specifiedByURL);
    }

    public function testExplicitSpecifiedByUrlTakesPrecedence(): void
    {
        self::requireSpecifiedByURLSupport();

        $date = new Date([
            'specifiedByURL' => 'https://example.com/custom-date-specification',
        ]);

        self::assertSame('https://example.com/custom-date-specification', $date->specifiedByURL);
    }

    public function testSchemaPrinterIncludesSpecifiedByDirective(): void
    {
        self::requireSpecifiedByURLSupport();

        $date = new Date();

        $schemaConfig = new SchemaConfig();
        $schemaConfig->setQuery(new ObjectType([
            'name' => 'Query',
            'fields' => [
                'date' => $date,
            ],
        ]));

        $schema = new Schema($schemaConfig);

        self::assertStringContainsString(
            'scalar Date @specifiedBy(url: "https://github.com/mll-lab/graphql-php-scalars#date")',
            SchemaPrinter::doPrint($schema)
        );
    }

    private static function requireSpecifiedByURLSupport(): void
    {
        if (! property_exists(ScalarType::class, 'specifiedByURL')) {
            self::markTestSkipped('webonyx/graphql-php does not support specifiedByURL yet.');
        }
    }
}
