<?php declare(strict_types=1);

namespace MLL\GraphQLScalars\Tests;

use MLL\GraphQLScalars\DateScalar;
use PHPUnit\Framework\TestCase;

final class DateScalarTest extends TestCase
{
    public function testThrowsIfRegexLacksDateGroup(): void
    {
        $dateScalar = new class extends DateScalar {
            public string $name = 'NoDateGroup';

            protected static function outputFormat(): string
            {
                return 'Y-m-d';
            }

            protected static function regex(): string
            {
                return '~^\d{4}-\d{2}-\d{2}$~';
            }
        };

        $this->expectExceptionObject(new \LogicException('Regex must define a named group "date": ' . $dateScalar::class . '.'));
        $dateScalar->parseValue('2020-04-20');
    }
}
