<?php

declare(strict_types=1);

namespace MLL\GraphQLScalars\Tests;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;
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
}
