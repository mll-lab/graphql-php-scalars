<?php declare(strict_types=1);

namespace MLL\GraphQLScalars\Tests;

use DateTimeImmutable;
use GraphQL\Error\Error;
use GraphQL\Error\InvariantViolation;
use GraphQL\Language\AST\IntValueNode;
use GraphQL\Language\AST\StringValueNode;
use MLL\GraphQLScalars\DateScalar;
use PHPUnit\Framework\TestCase;

abstract class DateScalarTest extends TestCase
{
    /**
     * @dataProvider invalidDateValues
     *
     * @param mixed $value An invalid value for a date
     */
    public function testThrowsIfSerializingInvalidDates($value): void
    {
        $this->expectException(InvariantViolation::class);

        $this->scalarInstance()->serialize($value);
    }

    /**
     * @dataProvider invalidDateValues
     *
     * @param mixed $value An invalid value for a date
     */
    public function testThrowsIfParseValueInvalidDate($value): void
    {
        $this->expectException(Error::class);

        $this->scalarInstance()->parseValue($value);
    }

    /**
     * Those values should fail passing as a date.
     *
     * @return iterable<array-key, array{mixed}>
     */
    public function invalidDateValues(): iterable
    {
        yield [1];
        yield ['rolf'];
        yield [null];
        yield [''];
    }

    /**
     * @dataProvider validDates
     */
    public function testParsesValueString(string $value, string $expected): void
    {
        $parsedValue = $this->scalarInstance()->parseValue($value);

        self::assertSame($expected, $parsedValue->format('Y-m-d\TH:i:s.uP'));
    }

    /**
     * @dataProvider validDates
     */
    public function testParsesLiteral(string $value, string $expected): void
    {
        $dateLiteral = new StringValueNode(
            ['value' => $value]
        );
        $parsed = $this->scalarInstance()->parseLiteral($dateLiteral);

        self::assertSame($expected, $parsed->format('Y-m-d\TH:i:s.uP'));
    }

    public function testThrowsIfParseLiteralNonString(): void
    {
        $this->expectException(Error::class);

        $this->scalarInstance()->parseLiteral(
            new IntValueNode([])
        );
    }

    public function testSerializesDateTimeInterfaceInstance(): void
    {
        $now = new DateTimeImmutable();
        $result = $this->scalarInstance()->serialize($now);

        self::assertNotEmpty($result);
    }

    /**
     * The specific instance under test.
     */
    abstract protected function scalarInstance(): DateScalar;

    /**
     * Data provider for valid date strings and expected dates.
     *
     * @return iterable<array{string, string}>
     */
    abstract public function validDates(): iterable;
}
